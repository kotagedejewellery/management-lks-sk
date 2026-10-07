<?php

namespace Tests\Unit;

use App\Models\LksPeriod;
use App\Models\PeriodActivity;
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

        $this->assertSame(['target_count' => 19, 'minimum_target_count' => 14], $result);
    }
}
