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

        if ($selectedDate->lt($period->start_date)) {
            $selectedDate = $period->start_date->copy()->startOfDay();
        } elseif ($selectedDate->gt($period->end_date)) {
            $selectedDate = $period->end_date->copy()->startOfDay();
        }

        $periodHasStarted = ! today()->startOfDay()->lt($period->start_date);
        $recordingAvailable = $period->recording_enabled
            && $periodHasStarted
            && ! today()->startOfDay()->gt($period->end_date);
        if (! $periodHasStarted) {
            return response()->json(['data' => [
                'viewer' => $viewerData,
                'period' => [
                    'id' => $period->getKey(),
                    'name' => $period->name,
                    'start_date' => $period->start_date->toDateString(),
                    'end_date' => $period->end_date->toDateString(),
                    'is_open' => false,
                    'recording_enabled' => $period->recording_enabled,
                    'recording_available' => false,
                ],
                'participant' => null,
                'personal_summary' => null,
                'activities' => [],
                'selected_date' => $selectedDate->toDateString(),
            ]]);
        }

        $participant = $period->participants()
            ->where('user_id', $request->user()->getKey())
            ->first();
        if ($participant !== null && $selectedDate->lt($participant->participation_start_date)) {
            $selectedDate = $participant->participation_start_date->copy()->startOfDay();
        }
        $participant?->load([
            'checklists' => fn ($query) => $query->whereDate('checklist_date', $selectedDate),
            'menstruationDispensations',
        ]);
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
        $isDispensationDate = $participant?->gender_snapshot === 'akhwat'
            && $participant->menstruationDispensations->contains(fn ($dispensation): bool => $selectedDate->betweenIncluded($dispensation->start_date, $dispensation->end_date));

        $activities = $period->periodActivities()
            ->orderBy('sort_order')
            ->get()
            ->filter(fn ($activity) => ($participant === null || $activity->appliesTo($participant))
                && ($participant === null || $activity->allowedWeekdaysFor($participant) === [] || in_array($selectedDate->isoWeekday(), $activity->allowedWeekdaysFor($participant), true)))
            ->map(function ($activity) use ($participant, $calculator, $period, $holidayDates, $holiday, $recordedDatesByActivity, $isDispensationDate): array {
                $checklist = $participant?->checklists->firstWhere('period_activity_id', $activity->getKey());
                $rule = $calculator->effectiveTargets($activity, $period, $holidayDates, $participant);
                $maxPerWeek = $participant === null ? null : $activity->maxPerWeekFor($participant);
                $menstruationExempt = $participant !== null && $activity->menstruationExemptFor($participant);
                $allowedWeekdays = $participant === null ? [] : $activity->allowedWeekdaysFor($participant);

                return [
                    'id' => $activity->getKey(),
                    'code' => $activity->activity_code_snapshot,
                    'name' => $activity->activity_name_snapshot,
                    'target_count' => $rule['target_count'],
                    'minimum_target_count' => $rule['minimum_target_count'],
                    'lks_category' => $rule['category'],
                    'max_per_week' => $maxPerWeek,
                    'menstruation_exempt' => $menstruationExempt,
                    'is_completed' => $checklist?->is_completed ?? false,
                    'recorded_dates' => $recordedDatesByActivity->get($activity->getKey(), []),
                    'allowed_weekdays' => $allowedWeekdays,
                    'is_optional_today' => $holiday !== null,
                    'holiday_name' => $holiday?->name,
                    'is_dispensation_today' => $isDispensationDate && $menstruationExempt,
                ];
            })
            ->filter(fn (array $activity): bool => $activity['target_count'] > 0 || $activity['is_dispensation_today'])
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
                'magang_passing_threshold' => $period->magang_passing_threshold,
                'is_open' => true,
                'recording_enabled' => $period->recording_enabled,
                'recording_available' => $recordingAvailable,
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
                'category' => $participant->category_snapshot === 'magang' ? 'magang' : 'regular',
                'gender' => $participant->gender_snapshot,
                'menstruation_dispensations' => $participant->menstruationDispensations
                    ->sortBy('start_date')
                    ->map(fn ($dispensation): array => [
                        'id' => $dispensation->getKey(),
                        'start_date' => $dispensation->start_date->toDateString(),
                        'end_date' => $dispensation->end_date->toDateString(),
                    ])
                    ->values(),
            ],
            'personal_summary' => $participant === null ? null : $calculator->calculate($participant),
            'activities' => $activities,
            'selected_date' => $selectedDate->toDateString(),
        ]]);
    }
}
