<?php

namespace Tests\Unit;

use App\Models\PeriodActivity;
use App\Models\PeriodParticipantSnapshot;
use Tests\TestCase;

class PeriodActivityRuleTest extends TestCase
{
    public function test_magang_uses_its_own_operational_rules(): void
    {
        $activity = new PeriodActivity([
            'is_active' => true,
            'allowed_weekdays' => '{1,2,3,4,5}',
            'applicable_genders' => '{ikhwan}',
            'applicable_levels' => '{staff}',
            'max_per_week' => 5,
            'magang_is_active' => true,
            'magang_allowed_weekdays' => '{6}',
            'magang_applicable_genders' => '{akhwat}',
            'magang_applicable_levels' => '{staff}',
            'magang_max_per_week' => 1,
        ]);
        $regular = new PeriodParticipantSnapshot(['category_snapshot' => 'regular', 'gender_snapshot' => 'ikhwan', 'level_snapshot' => 'staff']);
        $magang = new PeriodParticipantSnapshot(['category_snapshot' => 'magang', 'gender_snapshot' => 'akhwat', 'level_snapshot' => 'staff']);

        $this->assertTrue($activity->appliesTo($regular));
        $this->assertTrue($activity->appliesTo($magang));
        $this->assertSame([1, 2, 3, 4, 5], $activity->allowedWeekdaysFor($regular));
        $this->assertSame([6], $activity->allowedWeekdaysFor($magang));
        $this->assertSame(5, $activity->maxPerWeekFor($regular));
        $this->assertSame(1, $activity->maxPerWeekFor($magang));
    }

    public function test_spreadsheet_activity_scopes_are_applied_by_gender_and_job_level(): void
    {
        $puasaKamis = new PeriodActivity([
            'is_active' => true,
            'applicable_genders' => '{ikhwan,akhwat}',
            'applicable_levels' => '{leader}',
        ]);
        $subuhanMasjid = new PeriodActivity([
            'is_active' => true,
            'applicable_genders' => '{ikhwan}',
            'applicable_levels' => '{leader,staff}',
        ]);

        $ikhwanLeader = new PeriodParticipantSnapshot(['gender_snapshot' => 'ikhwan', 'level_snapshot' => 'leader']);
        $akhwatStaff = new PeriodParticipantSnapshot(['gender_snapshot' => 'akhwat', 'level_snapshot' => 'staff']);

        self::assertTrue($puasaKamis->appliesTo($ikhwanLeader));
        self::assertFalse($puasaKamis->appliesTo($akhwatStaff));
        self::assertTrue($subuhanMasjid->appliesTo($ikhwanLeader));
        self::assertFalse($subuhanMasjid->appliesTo($akhwatStaff));
    }
}
