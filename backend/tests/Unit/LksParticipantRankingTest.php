<?php

namespace Tests\Unit;

use App\Services\LksParticipantRanking;
use Illuminate\Support\Collection;
use Tests\TestCase;

class LksParticipantRankingTest extends TestCase
{
    public function test_it_ranks_only_staff_by_gender_without_repeating_top_participants(): void
    {
        $scores = new Collection([
            ['name' => 'Leader Ikhwan', 'level' => 'leader', 'gender' => 'ikhwan', 'final_percentage' => 100],
            ['name' => 'Ahmad', 'level' => 'staff', 'gender' => 'ikhwan', 'final_percentage' => 100],
            ['name' => 'Budi', 'level' => 'staff', 'gender' => 'ikhwan', 'final_percentage' => 90],
            ['name' => 'Candra', 'level' => 'staff', 'gender' => 'ikhwan', 'final_percentage' => 90],
            ['name' => 'Deni', 'level' => 'staff', 'gender' => 'ikhwan', 'final_percentage' => 80],
            ['name' => 'Eko', 'level' => 'staff', 'gender' => 'ikhwan', 'final_percentage' => 70],
            ['name' => 'Fajar', 'level' => 'staff', 'gender' => 'ikhwan', 'final_percentage' => 60],
            ['name' => 'Gilang', 'level' => 'staff', 'gender' => 'ikhwan', 'final_percentage' => 50],
            ['name' => 'Aisyah', 'level' => 'staff', 'gender' => 'akhwat', 'final_percentage' => 95],
        ]);

        $rankings = app(LksParticipantRanking::class)->forScores($scores);

        $this->assertSame(['Ahmad', 'Budi', 'Candra'], array_column($rankings['ikhwan']['highest'], 'name'));
        $this->assertSame(['Gilang', 'Fajar', 'Eko'], array_column($rankings['ikhwan']['lowest'], 'name'));
        $this->assertSame(['Aisyah'], array_column($rankings['akhwat']['highest'], 'name'));
        $this->assertSame([], $rankings['akhwat']['lowest']);
    }
}
