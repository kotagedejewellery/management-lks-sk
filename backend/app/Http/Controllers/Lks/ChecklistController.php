<?php

namespace App\Http\Controllers\Lks;

use App\Http\Controllers\Controller;
use App\Http\Requests\Lks\StoreChecklistRequest;
use App\Http\Requests\Lks\StoreBulkChecklistRequest;
use App\Models\AuditLog;
use App\Models\LksPeriod;
use App\Models\LksChecklist;
use App\Models\PeriodActivity;
use App\Models\PeriodParticipantSnapshot;
use App\Services\ChecklistRecordingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ChecklistController extends Controller
{
    public function store(StoreChecklistRequest $request, ChecklistRecordingService $service): JsonResponse
    {
        $data = $request->validated();
        $actor = $request->user();
        $activity = PeriodActivity::query()->findOrFail($data['period_activity_id']);

        $participant = isset($data['participant_id'])
            ? PeriodParticipantSnapshot::query()->findOrFail($data['participant_id'])
            : LksPeriod::query()
                ->where('status', 'active')
                ->firstOrFail()
                ->participants()
                ->where('user_id', $actor->getKey())
                ->firstOrFail();

        $checklist = $service->record(
            $actor,
            $participant,
            $activity,
            $data['checklist_date'],
            $request->boolean('is_completed'),
            $data['reason'] ?? null,
        );

        return response()->json(['data' => [
            'id' => $checklist->getKey(),
            'period_activity_id' => $checklist->period_activity_id,
            'checklist_date' => $checklist->checklist_date->toDateString(),
            'is_completed' => $checklist->is_completed,
        ]]);
    }

    public function storeBulk(StoreBulkChecklistRequest $request, ChecklistRecordingService $service): JsonResponse
    {
        $data = $request->validated();
        $actor = $request->user();
        $activity = PeriodActivity::query()->findOrFail($data['period_activity_id']);
        $participant = LksPeriod::query()
            ->where('status', 'active')
            ->firstOrFail()
            ->participants()
            ->where('user_id', $actor->getKey())
            ->firstOrFail();

        return response()->json(['data' => $service->recordBulk(
            $actor,
            $participant,
            $activity,
            $data['checklist_dates'],
        )]);
    }

    public function correctionContext(Request $request, LksPeriod $period, PeriodParticipantSnapshot $participant): JsonResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);
        $this->ensureParticipantBelongsToPeriod($period, $participant);

        $date = Carbon::parse($request->validate(['date' => ['nullable', 'date']])['date'] ?? now())->startOfDay();
        $minimumDate = $participant->participation_start_date->copy()->startOfDay();
        $maximumDate = now()->startOfDay();
        if ($period->end_date->lt($maximumDate)) {
            $maximumDate = $period->end_date->copy()->startOfDay();
        }

        if ($date->lt($minimumDate) || $date->gt($maximumDate)) {
            throw ValidationException::withMessages(['date' => 'Tanggal koreksi berada di luar masa partisipasi peserta.']);
        }

        $activities = $period->periodActivities()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->filter(fn (PeriodActivity $activity): bool => $activity->appliesTo($participant) && ($activity->allowedWeekdays() === [] || in_array($date->isoWeekday(), $activity->allowedWeekdays(), true)))
            ->values();
        $checklists = LksChecklist::query()
            ->where('period_participant_id', $participant->getKey())
            ->whereDate('checklist_date', $date)
            ->whereIn('period_activity_id', $activities->modelKeys())
            ->get()
            ->keyBy('period_activity_id');
        $corrections = AuditLog::query()
            ->with('actor:id,name')
            ->where('event', 'checklist.corrected')
            ->where('auditable_type', (new LksChecklist)->getMorphClass())
            ->whereIn('auditable_id', $checklists->modelKeys())
            ->latest('created_at')
            ->get()
            ->map(function (AuditLog $audit) use ($activities, $checklists): array {
                $after = $audit->after_data ?? [];
                $before = $audit->before_data ?? [];
                $activityId = $after['period_activity_id'] ?? $checklists->firstWhere('id', $audit->auditable_id)?->period_activity_id;

                return [
                    'activity' => $activities->firstWhere('id', $activityId)?->activity_name_snapshot,
                    'actor' => $audit->actor?->name ?? 'Admin',
                    'before' => $before['is_completed'] ?? null,
                    'after' => $after['is_completed'] ?? false,
                    'reason' => $audit->reason,
                    'created_at' => $audit->created_at?->toIso8601String(),
                ];
            })
            ->values();

        return response()->json(['data' => [
            'participant' => [
                'id' => $participant->getKey(),
                'name' => $participant->participant_name_snapshot,
                'team' => $participant->team_name_snapshot,
                'department' => $participant->department_name_snapshot,
            ],
            'date' => $date->toDateString(),
            'minimum_date' => $minimumDate->toDateString(),
            'maximum_date' => $maximumDate->toDateString(),
            'activities' => $activities->map(fn (PeriodActivity $activity): array => [
                'id' => $activity->getKey(),
                'name' => $activity->activity_name_snapshot,
                'is_completed' => (bool) ($checklists->get($activity->getKey())?->is_completed ?? false),
            ])->values(),
            'corrections' => $corrections,
        ]]);
    }

    public function correct(Request $request, LksPeriod $period, PeriodParticipantSnapshot $participant, ChecklistRecordingService $service): JsonResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);
        $this->ensureParticipantBelongsToPeriod($period, $participant);

        $data = $request->validate([
            'checklist_date' => ['required', 'date'],
            'reason' => ['required', 'string', 'max:1000'],
            'activities' => ['required', 'array', 'min:1'],
            'activities.*.period_activity_id' => ['required', 'uuid', 'distinct', 'exists:period_activities,id'],
            'activities.*.is_completed' => ['required', 'boolean'],
        ]);
        $activityIds = collect($data['activities'])->pluck('period_activity_id');
        $activities = $period->periodActivities()->whereIn('id', $activityIds)->get()->keyBy('id');

        if ($activities->count() !== $activityIds->count()) {
            throw ValidationException::withMessages(['activities' => 'Aktivitas harus berasal dari periode yang dipilih.']);
        }

        DB::transaction(function () use ($data, $activities, $request, $participant, $service): void {
            foreach ($data['activities'] as $change) {
                $service->record(
                    $request->user(),
                    $participant,
                    $activities->get($change['period_activity_id']),
                    $data['checklist_date'],
                    (bool) $change['is_completed'],
                    $data['reason'],
                );
            }
        });

        return response()->json(['data' => ['updated' => count($data['activities'])]]);
    }

    private function ensureParticipantBelongsToPeriod(LksPeriod $period, PeriodParticipantSnapshot $participant): void
    {
        abort_unless($participant->period_id === $period->getKey(), 404);
    }
}
