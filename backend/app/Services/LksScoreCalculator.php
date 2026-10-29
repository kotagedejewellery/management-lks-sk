<?php

namespace App\Services;

use App\Models\LksChecklist;
use App\Models\LksPeriod;
use App\Models\PeriodParticipantSnapshot;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;

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
        $holidayDates = $this->periodHolidayDates($participant->period);
        $effectiveTargets = $activities
            ->mapWithKeys(fn ($activity): array => [$activity->getKey() => $this->effectiveTargets($activity, $participant->period, $holidayDates, $participant)])
            ->filter(fn (array $rule): bool => $rule['target_count'] > 0);
        $activities = $activities->filter(fn ($activity): bool => $effectiveTargets->has($activity->getKey()))->values();
        $completed = LksChecklist::query()
            ->where('period_participant_id', $participant->getKey())
            ->where('is_completed', true)
            ->whereIn('period_activity_id', $activities->modelKeys())
            ->when($holidayDates !== [], fn ($query) => $query->whereNotIn('checklist_date', $holidayDates))
            ->groupBy('period_activity_id')
            ->selectRaw('period_activity_id, count(*) as total')
            ->pluck('total', 'period_activity_id');

        $totalWeight = 0.0;
        $weightedScore = 0.0;
        $activityScores = $activities->map(function ($activity) use ($completed, $effectiveTargets, &$totalWeight, &$weightedScore): array {
            $count = (int) ($completed[$activity->getKey()] ?? 0);
            $rule = $effectiveTargets[$activity->getKey()];
            $percentage = min($count / $rule['target_count'], 1) * 100;
            $weight = (float) $activity->weight;
            $totalWeight += $weight;
            $weightedScore += $percentage * $weight;

            return [
                'id' => $activity->getKey(),
                'name' => $activity->activity_name_snapshot,
                'target_count' => $rule['target_count'],
                'minimum_target_count' => $rule['minimum_target_count'],
                'completed_count' => $count,
                'percentage' => round($percentage, 2),
                'status' => $count >= $rule['minimum_target_count'] ? 'tuntas' : 'belum_tuntas',
            ];
        })->values();

        $finalPercentage = $totalWeight === 0.0 ? 0.0 : $weightedScore / $totalWeight;
        $passingThreshold = $this->passingThreshold($participant);
        $finalStatus = $finalPercentage >= $passingThreshold ? 'tuntas' : 'belum_tuntas';
        $grade = $activityScores->sum('completed_count') > 0 ? $this->grade($finalPercentage) : null;
        $recommendation = $participant->period->status === 'closed' && $participant->recommendation_snapshot !== null
            ? $participant->recommendation_snapshot
            : $this->recommendation($activityScores, $finalPercentage, $passingThreshold, $finalStatus);

        return [
            'participant_id' => $participant->getKey(),
            'user_id' => $participant->user_id,
            'name' => $participant->participant_name_snapshot,
            'gender' => $participant->gender_snapshot,
            'category' => $this->category($participant),
            'level' => $participant->level_snapshot,
            'participation_start_date' => $participant->participation_start_date?->toDateString(),
            'department_id' => $participant->department_id_snapshot,
            'department' => $participant->department_name_snapshot,
            'team' => $participant->team_name_snapshot,
            'leader_user_id' => $participant->leader_user_id_snapshot,
            'leader' => $participant->leader_name_snapshot,
            'activities' => $activityScores,
            'final_percentage' => round($finalPercentage, 2),
            'passing_threshold' => $passingThreshold,
            'final_status' => $finalStatus,
            'grade' => $grade,
            'recommendation' => $recommendation,
        ];
    }

    private function grade(float $percentage): string
    {
        return match (true) {
            $percentage >= 91 => 'A',
            $percentage >= 81 => 'B',
            $percentage >= 71 => 'C',
            $percentage >= 61 => 'D',
            default => 'E',
        };
    }

    /** @param Collection<int, array<string, mixed>> $activities */
    private function recommendation(Collection $activities, float $finalPercentage, float $passingThreshold, string $finalStatus): string
    {
        if ($activities->isEmpty()) {
            return 'Belum ada aktivitas yang berlaku pada periode ini.';
        }

        if ($finalPercentage === 0.0) {
            return 'Belum ada checklist. Mulai pencatatan hari ini dan fokus memenuhi batas minimal setiap aktivitas yang berlaku.';
        }

        $priorities = $activities->where('status', 'belum_tuntas')->sortBy('percentage')->take(3)->pluck('name')->implode(', ');
        if ($finalStatus !== 'tuntas') {
            $gap = $this->formatPercentage($passingThreshold - $finalPercentage);
            $score = $this->formatPercentage($finalPercentage);
            $threshold = $this->formatPercentage($passingThreshold);

            return $priorities === ''
                ? "Semua batas minimal aktivitas sudah terpenuhi, tetapi nilai Anda {$score}% masih {$gap} poin dari ambang {$threshold}%. Lanjutkan pencatatan untuk memperkuat capaian."
                : "Nilai Anda {$score}% masih {$gap} poin dari ambang {$threshold}%. Prioritaskan: {$priorities}.";
        }

        if ($priorities !== '') {
            return "Target periode sudah tercapai. Untuk menjaga konsistensi, tingkatkan: {$priorities}.";
        }

        return $finalPercentage >= min($passingThreshold + 10, 100)
            ? 'Capaian sangat baik dan konsisten. Pertahankan kebiasaan ini hingga periode berakhir.'
            : 'Target periode tercapai. Pertahankan konsistensi pencatatan hingga periode berakhir.';
    }

    private function formatPercentage(float $value): string
    {
        return rtrim(rtrim(number_format(max($value, 0), 1, '.', ''), '0'), '.');
    }

    /** @return Collection<int, array<string, mixed>> */
    public function calculatePeriod(LksPeriod $period, ?string $leaderUserId = null, ?string $userId = null): Collection
    {
        $participants = $period->participants()->orderBy('participant_name_snapshot');
        if ($leaderUserId !== null) {
            $participants
                ->where('leader_user_id_snapshot', $leaderUserId)
                ->where('user_id', '!=', $leaderUserId);
        }
        if ($userId !== null) {
            $participants->where('user_id', $userId);
        }

        return $participants->get()->map(fn (PeriodParticipantSnapshot $participant): array => $this->calculate($participant));
    }

    /** @param Collection<int, array<string, mixed>> $scores
     * @return Collection<int, array<string, mixed>>
     */
    public function departmentSummary(Collection $scores): Collection
    {
        return $scores->groupBy('department')->map(function (Collection $departmentScores, ?string $department): array {
            $average = $departmentScores->avg('final_percentage') ?? 0;

            return [
                'department' => $department ?? 'Tanpa departemen',
                'participant_count' => $departmentScores->count(),
                'average_percentage' => round($average, 2),
                'tuntas_count' => $departmentScores->where('final_status', 'tuntas')->count(),
                'belum_tuntas_count' => $departmentScores->where('final_status', 'belum_tuntas')->count(),
            ];
        })->values();
    }

    private function passingThreshold(PeriodParticipantSnapshot $participant): float
    {
        if ($participant->level_snapshot === 'leader') {
            return (float) $participant->period->final_passing_threshold;
        }

        return (float) ($this->category($participant) === 'magang'
            ? $participant->period->magang_passing_threshold ?? 75
            : $participant->period->staff_passing_threshold ?? 85);
    }

    /** @return list<string> */
    public function periodHolidayDates(LksPeriod $period): array
    {
        $period->loadMissing('holidaySnapshots');

        return $period->holidaySnapshots
            ->map(fn ($holiday): string => $holiday->holiday_date->toDateString())
            ->all();
    }

    /** @param list<string> $holidayDates
     *  @return array{target_count: int, minimum_target_count: int, category: string}
     */
    public function effectiveTargets($activity, LksPeriod $period, array $holidayDates = [], ?PeriodParticipantSnapshot $participant = null): array
    {
        $magang = $participant !== null && $this->category($participant) === 'magang';
        $target = (int) ($magang ? ($activity->magang_target_count ?? $activity->target_count) : $activity->target_count);
        $minimum = (int) ($magang ? ($activity->magang_minimum_target_count ?? $activity->minimum_target_count) : $activity->minimum_target_count);
        $weekdays = $activity->allowedWeekdays();
        $holidays = array_fill_keys($holidayDates, true);
        $fullEligibleDays = $this->eligibleDays($period->start_date, $period->end_date, $weekdays, $holidays);
        $baseTarget = $weekdays === [] ? $target : min($target, $fullEligibleDays);
        $baseMinimum = $baseTarget === 0 || $target === 0
            ? 0
            : min($baseTarget, (int) ceil($minimum / $target * $baseTarget));

        if ($participant === null || $participant->participation_start_date === null || $period->target_proration_enabled !== true) {
            return ['target_count' => $baseTarget, 'minimum_target_count' => $baseMinimum, 'category' => $magang ? 'magang' : 'regular'];
        }

        $participationStart = Carbon::parse($participant->participation_start_date)->startOfDay();
        if ($participationStart->lte($period->start_date)) {
            return ['target_count' => $baseTarget, 'minimum_target_count' => $baseMinimum, 'category' => $magang ? 'magang' : 'regular'];
        }

        $participantEligibleDays = $this->eligibleDays($participationStart, $period->end_date, $weekdays, $holidays);
        if ($fullEligibleDays === 0 || $participantEligibleDays === 0 || $baseTarget === 0) {
            return ['target_count' => 0, 'minimum_target_count' => 0, 'category' => $magang ? 'magang' : 'regular'];
        }

        $effectiveTarget = min($participantEligibleDays, (int) ceil($baseTarget * $participantEligibleDays / $fullEligibleDays));

        return [
            'target_count' => $effectiveTarget,
            'minimum_target_count' => min($effectiveTarget, (int) ceil($baseMinimum / $baseTarget * $effectiveTarget)),
            'category' => $magang ? 'magang' : 'regular',
        ];
    }

    /** @param list<int> $weekdays
     *  @param array<string, bool> $holidays
     */
    private function eligibleDays($startDate, $endDate, array $weekdays, array $holidays): int
    {
        $eligibleDays = 0;
        $date = Carbon::parse($startDate)->startOfDay();
        $endDate = Carbon::parse($endDate)->startOfDay();

        while ($date->lte($endDate)) {
            if (($weekdays === [] || in_array($date->isoWeekday(), $weekdays, true)) && ! isset($holidays[$date->toDateString()])) {
                $eligibleDays++;
            }
            $date->addDay();
        }

        return $eligibleDays;
    }

    private function category(PeriodParticipantSnapshot $participant): string
    {
        return $participant->category_snapshot === 'magang' ? 'magang' : 'regular';
    }
}
