<?php

namespace App\Services;

use App\Models\LksChecklist;
use App\Models\LksPeriod;
use App\Models\PeriodParticipantSnapshot;
use Illuminate\Support\Collection;

class LksScoreCalculator
{
    /** @return array<string, mixed> */
    public function calculate(PeriodParticipantSnapshot $participant): array
    {
        $participant->loadMissing('period');
        $activities = $participant->period->periodActivities()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->filter(fn ($activity) => $activity->appliesTo($participant))
            ->values();
        $completed = LksChecklist::query()
            ->where('period_participant_id', $participant->getKey())
            ->where('is_completed', true)
            ->whereIn('period_activity_id', $activities->modelKeys())
            ->groupBy('period_activity_id')
            ->selectRaw('period_activity_id, count(*) as total')
            ->pluck('total', 'period_activity_id');

        $totalWeight = 0.0;
        $weightedScore = 0.0;
        $activityScores = $activities->map(function ($activity) use ($completed, &$totalWeight, &$weightedScore): array {
            $count = (int) ($completed[$activity->getKey()] ?? 0);
            $percentage = min($count / $activity->target_count, 1) * 100;
            $weight = (float) $activity->weight;
            $totalWeight += $weight;
            $weightedScore += $percentage * $weight;

            return [
                'id' => $activity->getKey(),
                'name' => $activity->activity_name_snapshot,
                'target_count' => $activity->target_count,
                'minimum_target_count' => $activity->minimum_target_count,
                'completed_count' => $count,
                'percentage' => round($percentage, 2),
                'status' => $count >= $activity->minimum_target_count ? 'tuntas' : 'belum_tuntas',
            ];
        })->values();

        $finalPercentage = $totalWeight === 0.0 ? 0.0 : $weightedScore / $totalWeight;

        return [
            'participant_id' => $participant->getKey(),
            'user_id' => $participant->user_id,
            'name' => $participant->participant_name_snapshot,
            'gender' => $participant->gender_snapshot,
            'department' => $participant->department_name_snapshot,
            'team' => $participant->team_name_snapshot,
            'leader_user_id' => $participant->leader_user_id_snapshot,
            'leader' => $participant->leader_name_snapshot,
            'activities' => $activityScores,
            'final_percentage' => round($finalPercentage, 2),
            'final_status' => $finalPercentage >= (float) $participant->period->final_passing_threshold ? 'tuntas' : 'belum_tuntas',
        ];
    }

    /** @return Collection<int, array<string, mixed>> */
    public function calculatePeriod(LksPeriod $period, ?string $leaderUserId = null, ?string $userId = null): Collection
    {
        $participants = $period->participants()->orderBy('participant_name_snapshot');
        if ($leaderUserId !== null) {
            $participants->where('leader_user_id_snapshot', $leaderUserId);
        }
        if ($userId !== null) {
            $participants->where('user_id', $userId);
        }

        return $participants->get()->map(fn (PeriodParticipantSnapshot $participant): array => $this->calculate($participant));
    }

    /** @param Collection<int, array<string, mixed>> $scores
     * @return Collection<int, array<string, mixed>>
     */
    public function departmentSummary(Collection $scores, float $groupThreshold = 85): Collection
    {
        return $scores->groupBy('department')->map(function (Collection $departmentScores, ?string $department) use ($groupThreshold): array {
            $average = $departmentScores->avg('final_percentage') ?? 0;

            return [
                'department' => $department ?? 'Tanpa departemen',
                'participant_count' => $departmentScores->count(),
                'average_percentage' => round($average, 2),
                'tuntas_count' => $departmentScores->where('final_status', 'tuntas')->count(),
                'group_status' => $average >= $groupThreshold ? 'achieve' : 'not_achieve',
            ];
        })->values();
    }
}
