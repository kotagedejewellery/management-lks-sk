<?php

namespace Tests\Unit;

use App\Models\LksPeriod;
use App\Models\PeriodActivity;
use App\Models\PeriodParticipantSnapshot;
use App\Services\LksScoreCalculator;
use Tests\TestCase;

class EffectiveActivityTargetTest extends TestCase
{
    public function test_weekday_holidays_reduce_the_effective_target_and_keep_the_same_completion_ratio(): void
    {
        $period = new LksPeriod([
            'start_date' => '2026-10-01',
            'end_date' => '2026-10-31',
        ]);
        $activity = new PeriodActivity([
            'target_count' => 20,
            'minimum_target_count' => 14,
            'allowed_weekdays' => '{1,2,3,4,5}',
        ]);

        $result = (new LksScoreCalculator())->effectiveTargets($activity, $period, [
            '2026-10-01', '2026-10-02', '2026-10-05',
        ]);

        $this->assertSame(19, $result['target_count']);
        $this->assertSame(14, $result['minimum_target_count']);
        $this->assertSame('regular', $result['category']);
    }

    public function test_magang_participant_uses_the_magang_targets(): void
    {
        $period = new LksPeriod([
            'start_date' => '2026-10-01',
            'end_date' => '2026-10-31',
        ]);
        $activity = new PeriodActivity([
            'target_count' => 20,
            'minimum_target_count' => 14,
            'magang_target_count' => 15,
            'magang_minimum_target_count' => 11,
        ]);
        $participant = new PeriodParticipantSnapshot(['category_snapshot' => 'magang']);

        $result = (new LksScoreCalculator())->effectiveTargets($activity, $period, [], $participant);

        $this->assertSame(15, $result['target_count']);
        $this->assertSame(11, $result['minimum_target_count']);
        $this->assertSame('magang', $result['category']);
    }
}
