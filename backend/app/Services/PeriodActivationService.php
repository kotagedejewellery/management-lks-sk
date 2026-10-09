<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\DocumentSignatory;
use App\Models\LksPeriod;
use App\Models\PeriodParticipantSnapshot;
use App\Models\SantriProfile;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class PeriodActivationService
{
    public function __construct(private readonly LksScoreCalculator $calculator) {}

    public function activate(User $actor, LksPeriod $period): LksPeriod
    {
        $this->ensureAdmin($actor);

        return DB::transaction(function () use ($actor, $period): LksPeriod {
            $period = LksPeriod::query()->lockForUpdate()->findOrFail($period->getKey());
            $today = now()->startOfDay();

            if ($period->status !== 'draft') {
                throw ValidationException::withMessages(['period' => 'Hanya periode draft yang dapat diaktifkan.']);
            }

            if ($today->gt($period->end_date)) {
                throw ValidationException::withMessages(['period' => 'Periode yang sudah berakhir tidak dapat diaktifkan.']);
            }

            if (LksPeriod::query()->where('status', 'active')->exists()) {
                throw ValidationException::withMessages(['period' => 'Masih ada periode LKS aktif.']);
            }

            if (! $period->periodActivities()->where('is_active', true)->exists()) {
                throw ValidationException::withMessages(['period_activities' => 'Periode aktif harus memiliki minimal satu aktivitas aktif.']);
            }

            $signatories = DocumentSignatory::query()
                ->whereIn('role', ['general_manager', 'kabid_pengembangan_spiritual'])
                ->orderByRaw("CASE role WHEN 'general_manager' THEN 1 ELSE 2 END")
                ->get(['role', 'title', 'name', 'signature_path'])
                ->keyBy('role');
            $missingSignatories = collect(['general_manager', 'kabid_pengembangan_spiritual'])
                ->filter(fn (string $role): bool => blank($signatories->get($role)?->name)
                    || blank($signatories->get($role)?->signature_path)
                    || ! Storage::disk('local')->exists($signatories->get($role)->signature_path))
                ->map(fn (string $role): string => $role === 'general_manager' ? 'General Manager' : 'Kabid. Pengembangan Spiritual')
                ->values();

            if ($missingSignatories->isNotEmpty()) {
                throw ValidationException::withMessages([
                    'signatories' => 'Lengkapi nama dan TTD digital untuk: '.$missingSignatories->join(', ').'.',
                ]);
            }

            $profiles = SantriProfile::query()
                ->where('status', 'active')
                ->with(['user', 'department', 'team.leader'])
                ->get();

            foreach ($profiles as $profile) {
                $this->assertProfileOrganization($profile);
                $this->snapshot($period, $profile, $period->start_date->toDateString());
            }

            // MVP memakai bobot setara untuk seluruh aktivitas pada saat periode dikunci aktif.
            $period->periodActivities()->where('is_active', true)->update(['weight' => 1]);
            $period->update([
                'status' => 'active',
                'activated_at' => now(),
                'signatories_snapshot' => $signatories
                    ->values()
                    ->map(fn (DocumentSignatory $signatory): array => $this->snapshotSignatory($period, $signatory))
                    ->values()
                    ->all(),
            ]);

            AuditLog::create([
                'actor_user_id' => $actor->getKey(),
                'event' => 'period.activated',
                'auditable_type' => $period->getMorphClass(),
                'auditable_id' => $period->getKey(),
                'after_data' => ['status' => 'active', 'participant_count' => $profiles->count(), 'holiday_count' => $period->holidaySnapshots()->count()],
            ]);

            return $period->refresh();
        });
    }

    public function addParticipant(User $actor, LksPeriod $period, SantriProfile $profile): PeriodParticipantSnapshot
    {
        $this->ensureAdmin($actor);

        return DB::transaction(function () use ($actor, $period, $profile): PeriodParticipantSnapshot {
            $period = LksPeriod::query()->lockForUpdate()->findOrFail($period->getKey());
            $today = now()->startOfDay();

            if ($period->status !== 'active') {
                throw ValidationException::withMessages(['period' => 'Peserta manual hanya dapat ditambahkan pada periode aktif.']);
            }

            if ($today->lt($period->start_date) || $today->gt($period->end_date)) {
                throw ValidationException::withMessages(['period' => 'Tanggal penambahan peserta berada di luar periode.']);
            }

            $profile->loadMissing(['user', 'department', 'team.leader']);
            if ($profile->status !== 'active') {
                throw ValidationException::withMessages(['santri' => 'Hanya Santri Karya aktif yang dapat menjadi peserta.']);
            }

            $this->assertProfileOrganization($profile);
            $snapshot = $this->snapshot($period, $profile, $today->toDateString());

            if ($snapshot->wasRecentlyCreated) {
                AuditLog::create([
                    'actor_user_id' => $actor->getKey(),
                    'event' => 'period.participant_added',
                    'auditable_type' => $snapshot->getMorphClass(),
                    'auditable_id' => $snapshot->getKey(),
                    'after_data' => ['period_id' => $period->getKey(), 'user_id' => $profile->getKey()],
                ]);
            }

            return $snapshot;
        });
    }

    public function removeParticipant(User $actor, LksPeriod $period, PeriodParticipantSnapshot $participant): void
    {
        $this->ensureAdmin($actor);

        DB::transaction(function () use ($actor, $period, $participant): void {
            $period = LksPeriod::query()->lockForUpdate()->findOrFail($period->getKey());
            $participant = PeriodParticipantSnapshot::query()->lockForUpdate()->findOrFail($participant->getKey());

            if ($period->status !== 'active') {
                throw ValidationException::withMessages(['period' => 'Peserta hanya dapat dikelola pada periode aktif.']);
            }

            if ($participant->period_id !== $period->getKey()) {
                throw ValidationException::withMessages(['participant' => 'Peserta tidak terdaftar pada periode ini.']);
            }

            if ($participant->checklists()->exists()) {
                throw ValidationException::withMessages(['participant' => 'Peserta yang sudah memiliki checklist tidak dapat dikeluarkan agar riwayat tetap utuh.']);
            }

            AuditLog::create([
                'actor_user_id' => $actor->getKey(),
                'event' => 'period.participant_removed',
                'auditable_type' => $participant->getMorphClass(),
                'auditable_id' => $participant->getKey(),
                'before_data' => [
                    'period_id' => $period->getKey(),
                    'user_id' => $participant->user_id,
                    'participation_start_date' => $participant->participation_start_date->toDateString(),
                ],
            ]);

            $participant->delete();
        });
    }

    public function close(User $actor, LksPeriod $period): LksPeriod
    {
        $this->ensureAdmin($actor);

        return DB::transaction(function () use ($actor, $period): LksPeriod {
            $period = LksPeriod::query()->lockForUpdate()->findOrFail($period->getKey());

            if ($period->status !== 'active') {
                throw ValidationException::withMessages(['period' => 'Hanya periode aktif yang dapat ditutup.']);
            }

            $period->participants()->get()->each(function (PeriodParticipantSnapshot $participant): void {
                $participant->update(['recommendation_snapshot' => $this->calculator->calculate($participant)['recommendation']]);
            });

            $closedAt = now();
            $period->update(['status' => 'closed', 'closed_at' => $closedAt]);

            AuditLog::create([
                'actor_user_id' => $actor->getKey(),
                'event' => 'period.closed',
                'auditable_type' => $period->getMorphClass(),
                'auditable_id' => $period->getKey(),
                'before_data' => ['status' => 'active'],
                'after_data' => ['status' => 'closed', 'closed_at' => $closedAt->toIso8601String()],
            ]);

            return $period->refresh();
        });
    }

    private function ensureAdmin(User $actor): void
    {
        if (! $actor->isAdmin()) {
            throw new AuthorizationException('Hanya Admin yang dapat mengelola periode LKS.');
        }
    }

    private function assertProfileOrganization(SantriProfile $profile): void
    {
        if ($profile->team !== null && $profile->department_id !== $profile->team->department_id) {
            throw ValidationException::withMessages([
                'santri' => 'Departemen Santri Karya harus sama dengan departemen timnya.',
            ]);
        }
    }

    private function snapshot(LksPeriod $period, SantriProfile $profile, string $participationStartDate): PeriodParticipantSnapshot
    {
        return PeriodParticipantSnapshot::firstOrCreate(
            ['period_id' => $period->getKey(), 'user_id' => $profile->getKey()],
            [
                'participant_name_snapshot' => $profile->user->name,
                'gender_snapshot' => $profile->gender,
                'department_id_snapshot' => $profile->department?->getKey(),
                'department_name_snapshot' => $profile->department?->name,
                'team_id_snapshot' => $profile->team?->getKey(),
                'team_name_snapshot' => $profile->team?->name,
                'leader_user_id_snapshot' => $profile->team?->leader?->getKey(),
                'leader_name_snapshot' => $profile->team?->leader?->name,
                'category_snapshot' => $profile->category,
                'level_snapshot' => $profile->level,
                'participation_start_date' => $participationStartDate,
            ],
        );
    }

    /** @return array{role: string, title: string, name: ?string, signature_path: ?string} */
    private function snapshotSignatory(LksPeriod $period, DocumentSignatory $signatory): array
    {
        $snapshot = $signatory->only(['role', 'title', 'name', 'signature_path']);
        if ($signatory->signature_path === null || ! Storage::disk('local')->exists($signatory->signature_path)) {
            return $snapshot;
        }

        $extension = pathinfo($signatory->signature_path, PATHINFO_EXTENSION) ?: 'png';
        $path = "lks-signatures/periods/{$period->getKey()}/{$signatory->role}.{$extension}";
        Storage::disk('local')->copy($signatory->signature_path, $path);
        $snapshot['signature_path'] = $path;

        return $snapshot;
    }
}
