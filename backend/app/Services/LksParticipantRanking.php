<?php

namespace App\Services;

use Illuminate\Support\Collection;

class LksParticipantRanking
{
    /**
     * @param  Collection<int, array{name: string, level: string, gender: string, final_percentage: float}>  $scores
     * @return array{ikhwan: array{participant_count: int, highest: list<array<string, mixed>>, lowest: list<array<string, mixed>>}, akhwat: array{participant_count: int, highest: list<array<string, mixed>>, lowest: list<array<string, mixed>>}}
     */
    public function forScores(Collection $scores): array
    {
        $staff = $scores->where('level', 'staff');

        return collect(['ikhwan', 'akhwat'])->mapWithKeys(function (string $gender) use ($staff): array {
            $ranked = $staff
                ->where('gender', $gender)
                ->sort(function (array $left, array $right): int {
                    $percentage = $right['final_percentage'] <=> $left['final_percentage'];

                    return $percentage ?: strnatcasecmp($left['name'], $right['name']);
                })
                ->values();
            $highest = $ranked->take(3)->values();

            return [$gender => [
                'participant_count' => $ranked->count(),
                'highest' => $highest->all(),
                'lowest' => $ranked->slice(max($highest->count(), $ranked->count() - 3))->reverse()->values()->all(),
            ]];
        })->all();
    }
}
