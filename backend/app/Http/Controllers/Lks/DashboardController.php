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
        $holiday = $period->holidaySnapshots()->whereDate('holiday_date', $selectedDate)->first();
        $holidayDates = $calculator->periodHolidayDates($period);

        $activities = $period->periodActivities()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->filter(fn ($activity) => $participant === null || $activity->appliesTo($participant))
            ->map(function ($activity) use ($participant, $calculator, $period, $holidayDates, $holiday): array {
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
                'group_achievement_threshold' => $period->group_achievement_threshold,
                'is_open' => true,
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
