<?php

namespace App\Http\Controllers\Lks;

use App\Http\Controllers\Controller;
use App\Models\LksPeriod;
use App\Services\LksScoreCalculator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
            $periods->whereHas('participants', fn ($participants) => $participants->where('leader_user_id_snapshot', $viewer->getKey()));
        }

        $period = isset($request->period_id)
            ? $periods->findOrFail($request->period_id)
            : $periods->where('status', 'active')->firstOrFail();

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
            ],
            'participants' => $scores->values(),
            'summary' => [
                'participant_count' => $scores->count(),
                'average_percentage' => round($scores->avg('final_percentage') ?? 0, 2),
                'tuntas_count' => $scores->where('final_status', 'tuntas')->count(),
                'departments' => $viewer->isAdmin() ? $calculator->departmentSummary($scores) : [],
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
                ];
            })
            ->values();

        return response()->json(['data' => $trends]);
    }
}
