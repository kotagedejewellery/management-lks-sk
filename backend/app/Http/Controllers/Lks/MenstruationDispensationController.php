<?php

namespace App\Http\Controllers\Lks;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\LksPeriod;
use App\Models\PeriodMenstruationDispensation;
use App\Models\PeriodParticipantSnapshot;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MenstruationDispensationController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $period = LksPeriod::query()->where('status', 'active')->firstOrFail();
        $participant = $period->participants()->where('user_id', $request->user()->getKey())->firstOrFail();

        return response()->json(['data' => $this->create($request, $participant)] , 201);
    }

    public function destroy(Request $request, PeriodMenstruationDispensation $dispensation): Response
    {
        abort_unless($dispensation->participant->user_id === $request->user()->getKey(), 403);

        $this->delete($request, $dispensation);

        return response()->noContent();
    }

    public function storeForParticipant(Request $request, LksPeriod $period, PeriodParticipantSnapshot $participant): JsonResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);
        $this->ensureParticipantBelongsToPeriod($period, $participant);

        return response()->json(['data' => $this->create($request, $participant, true)], 201);
    }

    public function destroyForParticipant(Request $request, LksPeriod $period, PeriodParticipantSnapshot $participant, PeriodMenstruationDispensation $dispensation): Response
    {
        abort_unless($request->user()?->isAdmin(), 403);
        $this->ensureParticipantBelongsToPeriod($period, $participant);
        abort_unless($dispensation->period_participant_id === $participant->getKey(), 404);
        $reason = $request->validate(['reason' => ['required', 'string', 'max:1000']])['reason'];

        $this->delete($request, $dispensation, true, $reason);

        return response()->noContent();
    }

    /** @return array<string, string> */
    private function create(Request $request, PeriodParticipantSnapshot $participant, bool $isAdminCorrection = false): array
    {
        $data = $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => [$isAdminCorrection ? 'required' : 'nullable', 'string', 'max:1000'],
        ]);
        return DB::transaction(function () use ($request, $participant, $data, $isAdminCorrection): array {
            $participant = PeriodParticipantSnapshot::query()->lockForUpdate()->findOrFail($participant->getKey());
            $this->ensureCanChange($participant, $data['start_date'], $data['end_date']);
            $this->ensureDoesNotOverlap($participant, $data['start_date'], $data['end_date']);

            $dispensation = PeriodMenstruationDispensation::query()->create([
                'period_participant_id' => $participant->getKey(),
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'recorded_by_user_id' => $request->user()->getKey(),
            ]);

            AuditLog::create([
                'actor_user_id' => $request->user()->getKey(),
                'event' => $isAdminCorrection ? 'menstruation_dispensation.corrected' : 'menstruation_dispensation.recorded',
                'auditable_type' => $dispensation->getMorphClass(),
                'auditable_id' => $dispensation->getKey(),
                'after_data' => $this->auditData($dispensation),
                'reason' => $data['reason'] ?? null,
            ]);

            return $this->responseData($dispensation);
        });
    }

    private function delete(Request $request, PeriodMenstruationDispensation $dispensation, bool $isAdminCorrection = false, ?string $reason = null): void
    {
        $this->ensureCanChange($dispensation->participant, $dispensation->start_date, $dispensation->end_date);
        $before = $this->auditData($dispensation);
        $dispensation->delete();

        AuditLog::create([
            'actor_user_id' => $request->user()->getKey(),
            'event' => $isAdminCorrection ? 'menstruation_dispensation.correction_deleted' : 'menstruation_dispensation.deleted',
            'auditable_type' => (new PeriodMenstruationDispensation)->getMorphClass(),
            'auditable_id' => $dispensation->getKey(),
            'before_data' => $before,
            'reason' => $reason,
        ]);
    }

    private function ensureCanChange(PeriodParticipantSnapshot $participant, string|Carbon $startDate, string|Carbon $endDate): void
    {
        $participant->loadMissing('period');
        if ($participant->gender_snapshot !== 'akhwat') {
            throw ValidationException::withMessages(['dispensation' => 'Dispensasi ini hanya tersedia untuk peserta Akhwat.']);
        }
        if ($participant->period->status !== 'active') {
            throw ValidationException::withMessages(['period' => 'Dispensasi hanya dapat diubah pada periode aktif.']);
        }

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->startOfDay();
        $maximumDate = $participant->period->end_date->copy()->min(today())->startOfDay();
        if ($start->lt($participant->participation_start_date) || $end->gt($maximumDate)) {
            throw ValidationException::withMessages(['start_date' => 'Tanggal dispensasi harus berada dalam masa partisipasi dan tidak boleh melewati hari ini.']);
        }
    }

    private function ensureDoesNotOverlap(PeriodParticipantSnapshot $participant, string $startDate, string $endDate): void
    {
        if ($participant->menstruationDispensations()
            ->whereDate('start_date', '<=', $endDate)
            ->whereDate('end_date', '>=', $startDate)
            ->exists()) {
            throw ValidationException::withMessages(['start_date' => 'Rentang dispensasi tidak boleh bertumpang tindih dengan dispensasi yang sudah tercatat.']);
        }
    }

    /** @return array<string, string> */
    private function responseData(PeriodMenstruationDispensation $dispensation): array
    {
        return [
            'id' => $dispensation->getKey(),
            'start_date' => $dispensation->start_date->toDateString(),
            'end_date' => $dispensation->end_date->toDateString(),
        ];
    }

    /** @return array<string, string> */
    private function auditData(PeriodMenstruationDispensation $dispensation): array
    {
        return $this->responseData($dispensation) + ['period_participant_id' => $dispensation->period_participant_id];
    }

    private function ensureParticipantBelongsToPeriod(LksPeriod $period, PeriodParticipantSnapshot $participant): void
    {
        abort_unless($participant->period_id === $period->getKey(), 404);
    }
}
