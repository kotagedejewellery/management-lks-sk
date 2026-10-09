<?php

namespace App\Http\Controllers\Lks;

use App\Http\Controllers\Controller;
use App\Models\LksPeriod;
use App\Models\SantriProfile;
use App\Services\LksScoreCalculator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function __invoke(Request $request, LksScoreCalculator $calculator): JsonResponse
    {
        $viewer = $request->user()->loadMissing('roles');
        $selectedDate = Carbon::parse($request->validate(['date' => ['nullable', 'date']])['date'] ?? now())->startOfDay();
        $profile = SantriProfile::query()
            ->where('user_id', $viewer->getKey())
            ->with(['department:id,name', 'team:id,name,leader_user_id', 'team.leader:id,name'])
            ->first();
        $viewerData = [
            'id' => $viewer->getKey(),
            'name' => $viewer->name,
            'roles' => $viewer->roles->pluck('code')->values(),
            'must_change_password' => $viewer->must_change_password,
            'identity' => $profile === null ? null : [
                'team' => $profile->team?->name,
                'leader' => $profile->team?->leader?->name,
                'department' => $profile->department?->name,
                'level' => $profile->level,
            ],
        ];
        $period = LksPeriod::query()->where('status', 'active')->first();
        if ($period === null) {
            return response()->json(['data' => [
                'viewer' => $viewerData,
                'period' => null,
                'participant' => null,
                'personal_summary' => null,
                'activities' => [],
                'selected_date' => $selectedDate->toDateString(),
            ]]);
        }

        $periodHasStarted = ! today()->startOfDay()->lt($period->start_date);
        if (! $periodHasStarted) {
            return response()->json(['data' => [
                'viewer' => $viewerData,
                'period' => [
                    'id' => $period->getKey(),
                    'name' => $period->name,
                    'start_date' => $period->start_date->toDateString(),
                    'end_date' => $period->end_date->toDateString(),
                    'is_open' => false,
                ],
                'participant' => null,
                'personal_summary' => null,
                'activities' => [],
                'selected_date' => $selectedDate->toDateString(),
            ]]);
        }

        $participant = $period->participants()
            ->where('user_id', $request->user()->getKey())
            ->with(['checklists' => fn ($query) => $query->whereDate('checklist_date', $selectedDate)])
            ->first();
        $recordedDatesByActivity = $participant === null
            ? collect()
            : $participant->checklists()
                ->where('is_completed', true)
                ->whereBetween('checklist_date', [
                    $participant->participation_start_date->copy()->startOfDay(),
                    $period->end_date->isBefore(today()) ? $period->end_date->copy()->endOfDay() : today()->endOfDay(),
                ])
                ->orderBy('checklist_date')
                ->get(['period_activity_id', 'checklist_date'])
                ->groupBy('period_activity_id')
                ->map(fn ($checklists) => $checklists->pluck('checklist_date')->map->toDateString()->values()->all());
        $holiday = $period->holidaySnapshots()->whereDate('holiday_date', $selectedDate)->first();
        $holidayDates = $calculator->periodHolidayDates($period);

        $activities = $period->periodActivities()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->filter(fn ($activity) => ($participant === null || $activity->appliesTo($participant))
                && ($activity->allowedWeekdays() === [] || in_array($selectedDate->isoWeekday(), $activity->allowedWeekdays(), true)))
            ->map(function ($activity) use ($participant, $calculator, $period, $holidayDates, $holiday, $recordedDatesByActivity): array {
                $checklist = $participant?->checklists->firstWhere('period_activity_id', $activity->getKey());
                $rule = $calculator->effectiveTargets($activity, $period, $holidayDates);

                return [
                    'id' => $activity->getKey(),
                    'code' => $activity->activity_code_snapshot,
                    'name' => $activity->activity_name_snapshot,
                    'target_count' => $rule['target_count'],
                    'minimum_target_count' => $rule['minimum_target_count'],
                    'max_per_week' => $activity->max_per_week,
                    'is_completed' => $checklist?->is_completed ?? false,
                    'recorded_dates' => $recordedDatesByActivity->get($activity->getKey(), []),
                    'allowed_weekdays' => $activity->allowedWeekdays(),
                    'is_optional_today' => $holiday !== null,
                    'holiday_name' => $holiday?->name,
                ];
            })
            ->filter(fn (array $activity): bool => $activity['target_count'] > 0)
            ->values();

        return response()->json(['data' => [
            'viewer' => $viewerData,
            'period' => [
                'id' => $period->getKey(),
                'name' => $period->name,
                'start_date' => $period->start_date->toDateString(),
                'end_date' => $period->end_date->toDateString(),
                'leader_passing_threshold' => $period->final_passing_threshold,
                'staff_passing_threshold' => $period->staff_passing_threshold,
                'is_open' => true,
                'holidays' => $period->holidaySnapshots()
                    ->orderBy('holiday_date')
                    ->get(['holiday_date', 'name'])
                    ->map(fn ($holiday): array => [
                        'date' => $holiday->holiday_date->toDateString(),
                        'name' => $holiday->name,
                    ])
                    ->values(),
            ],
            'participant' => $participant === null ? null : [
                'id' => $participant->getKey(),
                'participation_start_date' => $participant->participation_start_date->toDateString(),
            ],
            'personal_summary' => $participant === null ? null : $calculator->calculate($participant),
            'activities' => $activities,
            'selected_date' => $selectedDate->toDateString(),
        ]]);
    }
}
