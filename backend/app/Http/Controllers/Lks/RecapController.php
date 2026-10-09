<?php

namespace App\Http\Controllers\Lks;

use App\Http\Controllers\Controller;
use App\Models\LksPeriod;
use App\Services\LksScoreCalculator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class RecapController extends Controller
{
    public function __invoke(Request $request, LksScoreCalculator $calculator): JsonResponse
    {
        $viewer = $request->user();
        $personalScope = $request->query('scope') === 'personal' && ! $viewer->isAdmin();
        $periods = LksPeriod::query()->whereIn('status', ['active', 'closed']);

        if ($personalScope || (! $viewer->isAdmin() && ! $viewer->hasRole('leader'))) {
            $periods->whereHas('participants', fn ($participants) => $participants->where('user_id', $viewer->getKey()));
        } elseif (! $viewer->isAdmin() && $viewer->hasRole('leader')) {
            $periods->whereHas('participants', fn ($participants) => $participants
                ->where('leader_user_id_snapshot', $viewer->getKey())
                ->where('user_id', '!=', $viewer->getKey()));
        }

        $period = isset($request->period_id)
            ? $periods->findOrFail($request->period_id)
            : $periods
                ->orderByRaw("case when status = 'active' then 0 else 1 end")
                ->orderByDesc('end_date')
                ->firstOrFail();

        if ($viewer->isAdmin()) {
            $scores = $calculator->calculatePeriod($period);
        } elseif ($personalScope || ! $viewer->hasRole('leader')) {
            $scores = $calculator->calculatePeriod($period, userId: $viewer->getKey());
        } elseif ($viewer->hasRole('leader')) {
            $scores = $calculator->calculatePeriod($period, leaderUserId: $viewer->getKey());
        }

        return response()->json(['data' => [
            'period' => [
                'id' => $period->getKey(),
                'name' => $period->name,
                'status' => $period->status,
                'start_date' => $period->start_date->toDateString(),
                'end_date' => $period->end_date->toDateString(),
                'closed_at' => $period->closed_at?->toIso8601String(),
                'leader_passing_threshold' => $period->final_passing_threshold,
                'staff_passing_threshold' => $period->staff_passing_threshold,
            ],
            'participants' => $scores->values(),
            'summary' => [
                'participant_count' => $scores->count(),
                'average_percentage' => round($scores->avg('final_percentage') ?? 0, 2),
                'tuntas_count' => $scores->where('final_status', 'tuntas')->count(),
                'belum_tuntas_count' => $scores->where('final_status', 'belum_tuntas')->count(),
                'departments' => $viewer->isAdmin() ? $calculator->departmentSummary($scores) : [],
                'recording_started_count' => $scores->filter(fn (array $score): bool => collect($score['activities'])->sum('completed_count') > 0)->count(),
                'activity_averages' => $viewer->isAdmin() ? $this->activityAverages($scores)->values() : [],
            ],
        ]]);
    }

    public function departmentTrends(Request $request, LksScoreCalculator $calculator): JsonResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);

        $trends = LksPeriod::query()
            ->whereIn('status', ['active', 'closed'])
            ->orderBy('start_date')
            ->get()
            ->map(function (LksPeriod $period) use ($calculator): array {
                $scores = $calculator->calculatePeriod($period);

                return [
                    'period' => [
                        'id' => $period->getKey(),
                        'name' => $period->name,
                        'status' => $period->status,
                        'start_date' => $period->start_date->toDateString(),
                        'end_date' => $period->end_date->toDateString(),
                    ],
                    'departments' => $calculator->departmentSummary($scores)->values(),
                    'groups' => [
                        'department' => $this->groupSummary($scores, 'department')->values(),
                        'leader' => $this->groupSummary($scores, 'leader')->values(),
                        'gender' => $this->groupSummary($scores, 'gender')->values(),
                    ],
                ];
            })
            ->values();

        return response()->json(['data' => $trends]);
    }

    public function roleTrends(Request $request, LksScoreCalculator $calculator): JsonResponse
    {
        $viewer = $request->user();
        abort_if($viewer->isAdmin(), 403);

        $leaderScope = $viewer->hasRole('leader');
        $periods = LksPeriod::query()->whereIn('status', ['active', 'closed']);

        if ($leaderScope) {
            $periods->whereHas('participants', fn ($participants) => $participants
                ->where('leader_user_id_snapshot', $viewer->getKey())
                ->where('user_id', '!=', $viewer->getKey()));
        } else {
            $periods->whereHas('participants', fn ($participants) => $participants->where('user_id', $viewer->getKey()));
        }

        $trends = $periods->orderByDesc('start_date')
            ->limit(6)
            ->get()
            ->sortBy('start_date')
            ->map(function (LksPeriod $period) use ($calculator, $leaderScope, $viewer): array {
                $scores = $leaderScope
                    ? $calculator->calculatePeriod($period, leaderUserId: $viewer->getKey())
                    : $calculator->calculatePeriod($period, userId: $viewer->getKey());

                return [
                    'period' => [
                        'id' => $period->getKey(),
                        'name' => $period->name,
                        'status' => $period->status,
                        'start_date' => $period->start_date->toDateString(),
                        'end_date' => $period->end_date->toDateString(),
                    ],
                    'participant_count' => $scores->count(),
                    'average_percentage' => $scores->isEmpty() ? null : round($scores->avg('final_percentage'), 2),
                ];
            })
            ->values();

        return response()->json(['data' => $trends]);
    }

    /** @return Collection<int, array{id: string, name: string, participant_count: int, average_percentage: float, tuntas_count: int}> */
    private function groupSummary(Collection $scores, string $dimension): Collection
    {
        return $scores->groupBy(function (array $score) use ($dimension): string {
            return match ($dimension) {
                'leader' => $score['leader_user_id'] ?? 'unassigned',
                'gender' => $score['gender'] ?? 'unknown',
                default => $score['department_id'] ?? 'unassigned',
            };
        })->map(function (Collection $group, string $id) use ($dimension): array {
            $first = $group->first();
            $name = match ($dimension) {
                'leader' => $first['leader'] ?? 'Belum ditetapkan',
                'gender' => ['ikhwan' => 'Ikhwan', 'akhwat' => 'Akhwat'][$id] ?? 'Tidak dicatat',
                default => $first['department'] ?? 'Tanpa departemen',
            };

            return [
                'id' => $id,
                'name' => $name,
                'participant_count' => $group->count(),
                'average_percentage' => round($group->avg('final_percentage') ?? 0, 2),
                'tuntas_count' => $group->where('final_status', 'tuntas')->count(),
            ];
        })->sortByDesc('average_percentage')->values();
    }

    /** @return Collection<int, array{id: string, name: string, average_percentage: float, participant_count: int}> */
    private function activityAverages(Collection $scores): Collection
    {
        return $scores->flatMap(fn (array $score) => $score['activities'])
            ->groupBy('id')
            ->map(function (Collection $activities, string $id): array {
                $first = $activities->first();

                return [
                    'id' => $id,
                    'name' => $first['name'],
                    'average_percentage' => round($activities->avg('percentage') ?? 0, 2),
                    'participant_count' => $activities->count(),
                ];
            })
            ->sortBy('average_percentage')
            ->values();
    }
}
