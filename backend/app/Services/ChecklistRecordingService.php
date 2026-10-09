<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\LksChecklist;
use App\Models\PeriodActivity;
use App\Models\PeriodParticipantSnapshot;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ChecklistRecordingService
{
    /**
     * @param  array<int, string>  $checklistDates
     * @return array{recorded: int, skipped: int}
     */
    public function recordBulk(User $actor, PeriodParticipantSnapshot $participant, PeriodActivity $activity, array $checklistDates): array
    {
        if ($actor->isAdmin()) {
            throw new AuthorizationException('Pencatatan beberapa tanggal hanya tersedia untuk LKS pribadi.');
        }

        $this->ensureCanRecord($actor, $participant);

        return DB::transaction(function () use ($participant, $activity, $checklistDates): array {
            $participant = PeriodParticipantSnapshot::query()->with('period.holidaySnapshots')->lockForUpdate()->findOrFail($participant->getKey());
            $activity = PeriodActivity::query()->lockForUpdate()->findOrFail($activity->getKey());
            $dates = collect($checklistDates)
                ->map(fn (string $date): Carbon => Carbon::parse($date)->startOfDay())
                ->unique(fn (Carbon $date): string => $date->toDateString())
                ->sortBy(fn (Carbon $date): string => $date->toDateString());
            $recorded = 0;
            $skipped = 0;
            $pending = [];
            $pendingWeeklyCounts = [];
            $errors = [];
            $holidayDates = $this->holidayDates($participant->period);

            foreach ($dates as $date) {
                $checklist = LksChecklist::query()
                    ->where('period_participant_id', $participant->getKey())
                    ->where('period_activity_id', $activity->getKey())
                    ->whereDate('checklist_date', $date)
                    ->lockForUpdate()
                    ->first();

                if ($checklist?->is_completed) {
                    $skipped++;
                    continue;
                }

                try {
                    $this->validateRecord($participant, $activity, $date, true, $holidayDates, $pendingWeeklyCounts);
                } catch (ValidationException $exception) {
                    $errors['checklist_dates.'.$date->toDateString()] = collect($exception->errors())->flatten()->first();
                    continue;
                }

                if ($activity->max_per_week !== null && ! in_array($date->toDateString(), $holidayDates, true)) {
                    $weekKey = $date->copy()->startOfWeek()->toDateString();
                    $pendingWeeklyCounts[$weekKey] = ($pendingWeeklyCounts[$weekKey] ?? 0) + 1;
                }
                $pending[] = compact('checklist', 'date');
            }

            if ($errors !== []) {
                throw ValidationException::withMessages(['checklist_dates' => 'Beberapa tanggal tidak lagi dapat dicatat. Periksa tanggal yang ditandai.'] + $errors);
            }

            foreach ($pending as ['checklist' => $checklist, 'date' => $date]) {
                $checklist ??= new LksChecklist([
                    'period_participant_id' => $participant->getKey(),
                    'period_activity_id' => $activity->getKey(),
                    'checklist_date' => $date,
                ]);
                $checklist->fill(['is_completed' => true, 'recorded_by_user_id' => $participant->user_id]);
                $checklist->save();
                $recorded++;
            }

            return ['recorded' => $recorded, 'skipped' => $skipped];
        });
    }

    public function record(
        User $actor,
        PeriodParticipantSnapshot $participant,
        PeriodActivity $activity,
        CarbonInterface|string $checklistDate,
        bool $isCompleted,
        ?string $reason = null,
    ): LksChecklist {
        $this->ensureCanRecord($actor, $participant);

        return DB::transaction(function () use ($actor, $participant, $activity, $checklistDate, $isCompleted, $reason): LksChecklist {
            $participant = PeriodParticipantSnapshot::query()->with('period.holidaySnapshots')->lockForUpdate()->findOrFail($participant->getKey());
            $activity = PeriodActivity::query()->lockForUpdate()->findOrFail($activity->getKey());
            $date = Carbon::parse($checklistDate)->startOfDay();

            $this->validateRecord($participant, $activity, $date, $isCompleted, $this->holidayDates($participant->period));

            $checklist = LksChecklist::query()
                ->where('period_participant_id', $participant->getKey())
                ->where('period_activity_id', $activity->getKey())
                ->whereDate('checklist_date', $date)
                ->lockForUpdate()
                ->first();

            $before = $checklist === null ? null : $this->auditData($checklist);
            if ($actor->isAdmin() && blank($reason)) {
                throw ValidationException::withMessages(['reason' => 'Alasan wajib diisi saat Admin mengoreksi checklist.']);
            }

            $checklist ??= new LksChecklist([
                'period_participant_id' => $participant->getKey(),
                'period_activity_id' => $activity->getKey(),
                'checklist_date' => $date,
            ]);

            $checklist->fill([
                'is_completed' => $isCompleted,
                'recorded_by_user_id' => $actor->getKey(),
            ]);
            $checklist->save();

            if ($actor->isAdmin()) {
                AuditLog::create([
                    'actor_user_id' => $actor->getKey(),
                    'event' => 'checklist.corrected',
                    'auditable_type' => $checklist->getMorphClass(),
                    'auditable_id' => $checklist->getKey(),
                    'before_data' => $before,
                    'after_data' => $this->auditData($checklist),
                    'reason' => $reason,
                ]);
            }

            return $checklist->refresh();
        });
    }

    private function ensureCanRecord(User $actor, PeriodParticipantSnapshot $participant): void
    {
        if (! $actor->isAdmin() && $actor->getKey() !== $participant->user_id) {
            throw new AuthorizationException('Checklist hanya dapat diisi oleh pemilik data atau Admin.');
        }
    }

    /** @param list<string> $holidayDates
     *  @param array<string, int> $pendingWeeklyCounts
     */
    private function validateRecord(PeriodParticipantSnapshot $participant, PeriodActivity $activity, Carbon $date, bool $isCompleted, array $holidayDates = [], array $pendingWeeklyCounts = []): void
    {
        $period = $participant->period;

        if ($period->status !== 'active' || $activity->period_id !== $period->getKey() || ! $activity->is_active) {
            throw ValidationException::withMessages(['checklist' => 'Checklist hanya dapat diisi pada aktivitas periode yang aktif.']);
        }

        if ($date->lt($period->start_date) || $date->gt($period->end_date) || $date->lt($participant->participation_start_date)) {
            throw ValidationException::withMessages(['checklist_date' => 'Tanggal checklist berada di luar masa partisipasi peserta.']);
        }

        if ($date->gt(now()->startOfDay())) {
            throw ValidationException::withMessages(['checklist_date' => 'Checklist tidak dapat diisi untuk tanggal mendatang.']);
        }

        $allowedWeekdays = $activity->allowedWeekdays();
        if ($allowedWeekdays !== [] && ! in_array($date->isoWeekday(), $allowedWeekdays, true)) {
            throw ValidationException::withMessages(['checklist_date' => 'Aktivitas tidak dijadwalkan pada hari tersebut.']);
        }

        if (! $activity->appliesTo($participant)) {
            throw ValidationException::withMessages(['checklist' => 'Aktivitas ini tidak berlaku untuk kategori peserta tersebut.']);
        }

        if ($isCompleted && $activity->max_per_week !== null && ! in_array($date->toDateString(), $holidayDates, true)) {
            $weeklyCount = LksChecklist::query()
                ->where('period_participant_id', $participant->getKey())
                ->where('period_activity_id', $activity->getKey())
                ->where('is_completed', true)
                ->whereBetween('checklist_date', [$date->copy()->startOfWeek(), $date->copy()->endOfWeek()])
                ->whereDate('checklist_date', '!=', $date)
                ->when($holidayDates !== [], fn ($query) => $query->whereNotIn('checklist_date', $holidayDates))
                ->count();

            $weeklyCount += $pendingWeeklyCounts[$date->copy()->startOfWeek()->toDateString()] ?? 0;

            if ($weeklyCount >= $activity->max_per_week) {
                throw ValidationException::withMessages(['checklist' => 'Batas pencatatan aktivitas untuk pekan ini sudah tercapai.']);
            }
        }
    }

    /** @return list<string> */
    private function holidayDates($period): array
    {
        $period->loadMissing('holidaySnapshots');

        return $period->holidaySnapshots->map(fn ($holiday): string => $holiday->holiday_date->toDateString())->all();
    }

    /** @return array<string, mixed> */
    private function auditData(LksChecklist $checklist): array
    {
        return [
            'period_activity_id' => $checklist->period_activity_id,
            'is_completed' => $checklist->is_completed,
            'checklist_date' => $checklist->checklist_date->toDateString(),
            'recorded_by_user_id' => $checklist->recorded_by_user_id,
        ];
    }
}
