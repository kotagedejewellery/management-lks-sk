<?php

namespace Tests\Unit;

use App\Models\SantriProfile;
use Tests\TestCase;

class SantriProfileCategoryTest extends TestCase
{
    public function test_employment_start_date_changes_category_after_three_calendar_months(): void
    {
        $profile = new SantriProfile(['employment_started_on' => '2026-01-10', 'category' => 'regular']);

        $this->assertSame('magang', $profile->lksCategoryAt('2026-04-09'));
        $this->assertSame('regular', $profile->lksCategoryAt('2026-04-10'));
    }

    public function test_end_of_month_start_uses_the_last_day_of_the_third_month_as_the_regular_boundary(): void
    {
        $profile = new SantriProfile(['employment_started_on' => '2026-01-31', 'category' => 'regular']);

        $this->assertSame('magang', $profile->lksCategoryAt('2026-04-29'));
        $this->assertSame('regular', $profile->lksCategoryAt('2026-04-30'));
    }

    public function test_legacy_profile_keeps_stored_category_without_employment_start_date(): void
    {
        $profile = new SantriProfile(['category' => 'magang']);

        $this->assertSame('magang', $profile->lksCategoryAt('2026-12-31'));
    }
}
