<?php

namespace Tests\Unit;

use App\Models\PeriodActivity;
use App\Models\PeriodParticipantSnapshot;
use PHPUnit\Framework\TestCase;

class PeriodActivityRuleTest extends TestCase
{
    public function test_spreadsheet_activity_scopes_are_applied_by_gender_and_job_level(): void
    {
        $puasaKamis = new PeriodActivity([
            'applicable_genders' => '{ikhwan,akhwat}',
            'applicable_levels' => '{leader}',
        ]);
        $subuhanMasjid = new PeriodActivity([
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
