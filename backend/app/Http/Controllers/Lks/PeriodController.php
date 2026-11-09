<?php

namespace App\Http\Controllers\Lks;

use App\Http\Controllers\Controller;
use App\Http\Requests\Lks\ActivatePeriodRequest;
use App\Http\Requests\Lks\AddPeriodParticipantRequest;
use App\Models\LksPeriod;
use App\Models\PeriodParticipantSnapshot;
use App\Models\SantriProfile;
use App\Services\PeriodActivationService;
use App\Services\LksScoreCalculator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class PeriodController extends Controller
{
    public function activate(
        ActivatePeriodRequest $request,
        LksPeriod $period,
        PeriodActivationService $service,
    ): JsonResponse {
        $period = $service->activate($request->user(), $period);

        return response()->json(['data' => [
            'id' => $period->getKey(),
            'status' => $period->status,
            'activated_at' => $period->activated_at?->toIso8601String(),
        ]]);
    }

    public function addParticipant(
        AddPeriodParticipantRequest $request,
        LksPeriod $period,
        SantriProfile $profile,
        PeriodActivationService $service,
    ): JsonResponse {
        $participant = $service->addParticipant($request->user(), $period, $profile);

        return response()->json(['data' => [
            'id' => $participant->getKey(),
            'user_id' => $participant->user_id,
            'participation_start_date' => $participant->participation_start_date->toDateString(),
        ]], $participant->wasRecentlyCreated ? 201 : 200);
    }

    public function participants(Request $request, LksPeriod $period): JsonResponse
    {
        abort_unless($request->user()->isAdmin(), 403);

        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'department_id' => ['nullable', 'uuid'],
            'team_id' => ['nullable', 'uuid'],
            'checklist_status' => ['nullable', 'in:none,recorded'],
            'category' => ['nullable', 'in:regular,magang'],
        ]);
        $search = trim((string) ($filters['search'] ?? ''));
        $participants = $period->participants()
            ->withCount('checklists')
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('participant_name_snapshot', 'ilike', "%{$search}%")
                    ->orWhere('department_name_snapshot', 'ilike', "%{$search}%")
                    ->orWhere('team_name_snapshot', 'ilike', "%{$search}%");
            }))
            ->when($filters['department_id'] ?? null, fn ($query, $departmentId) => $query->where('department_id_snapshot', $departmentId))
            ->when($filters['team_id'] ?? null, fn ($query, $teamId) => $query->where('team_id_snapshot', $teamId))
            ->when(($filters['checklist_status'] ?? null) === 'none', fn ($query) => $query->doesntHave('checklists'))
            ->when(($filters['checklist_status'] ?? null) === 'recorded', fn ($query) => $query->has('checklists'))
            ->when(($filters['category'] ?? null) === 'magang', fn ($query) => $query->where('category_snapshot', 'magang'))
            ->when(($filters['category'] ?? null) === 'regular', fn ($query) => $query->where(function ($query) {
                $query->where('category_snapshot', 'regular')->orWhereNull('category_snapshot');
            }))
            ->orderBy('participant_name_snapshot')
            ->paginate(25);

        return response()->json(['data' => [
            'participants' => collect($participants->items())->map(fn (PeriodParticipantSnapshot $participant) => [
                'id' => $participant->getKey(),
                'user_id' => $participant->user_id,
                'name' => $participant->participant_name_snapshot,
                'department' => $participant->department_name_snapshot,
                'team' => $participant->team_name_snapshot,
                'participation_start_date' => $participant->participation_start_date->toDateString(),
                'checklists_count' => $participant->checklists_count,
            ])->values(),
            'pagination' => [
                'current_page' => $participants->currentPage(),
                'last_page' => $participants->lastPage(),
                'per_page' => $participants->perPage(),
                'total' => $participants->total(),
            ],
        ]]);
    }

    public function removeParticipant(
        Request $request,
        LksPeriod $period,
        PeriodParticipantSnapshot $participant,
        PeriodActivationService $service,
    ): Response {
        $service->removeParticipant($request->user(), $period, $participant);

        return response()->noContent();
    }

    public function close(
        Request $request,
        LksPeriod $period,
        PeriodActivationService $service,
    ): JsonResponse {
        $period = $service->close($request->user(), $period);

        return response()->json(['data' => [
            'id' => $period->getKey(),
            'status' => $period->status,
            'closed_at' => $period->closed_at?->toIso8601String(),
        ]]);
    }

    public function updateRecording(
        Request $request,
        LksPeriod $period,
        PeriodActivationService $service,
    ): JsonResponse {
        $data = $request->validate(['recording_enabled' => ['required', 'boolean']]);
        $period = $service->setRecording($request->user(), $period, (bool) $data['recording_enabled']);

        return response()->json(['data' => [
            'id' => $period->getKey(),
            'status' => $period->status,
            'recording_enabled' => $period->recording_enabled,
        ]]);
    }

    public function history(Request $request, LksScoreCalculator $calculator): JsonResponse
    {
        $viewer = $request->user();
        $personalScope = $request->query('scope') === 'personal';
        $filters = $request->validate([
            'year' => ['nullable', 'integer', 'between:2000,2100'],
            'from' => ['nullable', 'date_format:Y-m-d', 'required_with:to'],
            'to' => ['nullable', 'date_format:Y-m-d', 'required_with:from', 'after_or_equal:from'],
            'matrix' => ['nullable', 'boolean'],
        ]);
        $includeMatrix = (bool) ($filters['matrix'] ?? false);
        abort_if($includeMatrix && ! $viewer->isAdmin(), 403);
        $hasDateRange = isset($filters['from'], $filters['to']);
        $periodQuery = LksPeriod::query()
            ->where('status', 'closed')
            ->when(
                ! $viewer->isAdmin() && ($personalScope || ! $viewer->hasRole('leader')),
                fn ($query) => $query->whereHas('participants', fn ($participants) => $participants->where('user_id', $viewer->getKey())),
            )
            ->when(
                ! $viewer->isAdmin() && ! $personalScope && $viewer->hasRole('leader'),
                fn ($query) => $query->whereHas('participants', fn ($participants) => $participants
                    ->where('leader_user_id_snapshot', $viewer->getKey())
                    ->where('user_id', '!=', $viewer->getKey())),
            );
        $availableYears = (clone $periodQuery)
            ->selectRaw('EXTRACT(YEAR FROM end_date) AS year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->map(fn ($year): string => (string) $year)
            ->values();
        $selectedYear = ! $hasDateRange && isset($filters['year']) ? (string) $filters['year'] : ($hasDateRange ? null : $availableYears->first());
        $selectedRange = $hasDateRange
            ? ['from' => $filters['from'], 'to' => $filters['to']]
            : ($selectedYear === null ? null : ['from' => "{$selectedYear}-01-01", 'to' => "{$selectedYear}-12-31"]);
        $scoredPeriods = $periodQuery
            ->when($hasDateRange, fn ($query) => $query->whereDate('end_date', '>=', $filters['from'])->whereDate('end_date', '<=', $filters['to']))
            ->when(! $hasDateRange && $selectedYear !== null, fn ($query) => $query->whereYear('end_date', $selectedYear))
            ->orderByDesc('end_date')
            ->get()
            ->map(function (LksPeriod $period) use ($calculator, $viewer, $personalScope): array {
                $scores = $viewer->isAdmin()
                    ? $calculator->calculatePeriod($period)
                    : ($personalScope || ! $viewer->hasRole('leader')
                        ? $calculator->calculatePeriod($period, userId: $viewer->getKey())
                        : $calculator->calculatePeriod($period, leaderUserId: $viewer->getKey()));

                return ['period' => $period, 'scores' => $scores];
            });
        $periods = $scoredPeriods
            ->map(function (array $scoredPeriod): array {
                /** @var LksPeriod $period */
                $period = $scoredPeriod['period'];
                /** @var Collection<int, array<string, mixed>> $scores */
                $scores = $scoredPeriod['scores'];
                $personal = $scores->first();
                $tuntasCount = $scores->where('final_status', 'tuntas')->count();

                return [
                    'id' => $period->getKey(),
                    'name' => $period->name,
                    'start_date' => $period->start_date->toDateString(),
                    'end_date' => $period->end_date->toDateString(),
                    'closed_at' => $period->closed_at?->toIso8601String(),
                    'statement' => [
                        'participant_count' => $scores->count(),
                        'final_percentage' => $personal['final_percentage'] ?? null,
                        'final_status' => $personal['final_status'] ?? null,
                        'grade' => $personal['grade'] ?? null,
                        'average_percentage' => $scores->isEmpty() ? null : round($scores->avg('final_percentage'), 2),
                        'tuntas_count' => $tuntasCount,
                        'belum_tuntas_count' => $scores->count() - $tuntasCount,
                    ],
                ];
            });

        $meta = [
            'available_years' => $availableYears,
            'selected_year' => $selectedYear,
            'selected_range' => $selectedRange,
        ];
        if ($includeMatrix) {
            $meta['annual_matrix'] = $this->annualMatrix($scoredPeriods);
        }

        return response()->json(['data' => $periods, 'meta' => $meta]);
    }

    /** @param Collection<int, array{period: LksPeriod, scores: Collection<int, array<string, mixed>>}> $scoredPeriods */
    private function annualMatrix(Collection $scoredPeriods): array
    {
        $participantGroups = ['ikhwan' => [], 'akhwat' => [], 'leader' => []];
        $departments = [];

        foreach ($scoredPeriods as $scoredPeriod) {
            $period = $scoredPeriod['period'];
            $periodId = (string) $period->getKey();

            foreach ($scoredPeriod['scores'] as $score) {
                $value = (float) $score['final_percentage'];
                $department = $score['department'] ?: 'Tanpa departemen';
                if (! isset($departments[$department])) {
                    $departments[$department] = ['name' => $department, 'cells' => []];
                }
                $departments[$department]['cells'][$periodId][] = $value;

                $group = $score['level'] === 'leader'
                    ? 'leader'
                    : ($score['gender'] === 'akhwat' ? 'akhwat' : 'ikhwan');
                if ($group !== 'leader' && $score['level'] !== 'staff') {
                    continue;
                }

                $userId = (string) $score['user_id'];
                if (! isset($participantGroups[$group][$userId])) {
                    $participantGroups[$group][$userId] = [
                        'name' => $score['name'],
                        'department' => $department,
                        'cells' => [],
                    ];
                }
                $participantGroups[$group][$userId]['cells'][$periodId] = $value;
            }
        }

        foreach ($departments as &$department) {
            foreach ($department['cells'] as $periodId => $scores) {
                $department['cells'][$periodId] = round(array_sum($scores) / count($scores), 2);
            }
        }
        unset($department);

        return [
            'periods' => $scoredPeriods->map(fn (array $scoredPeriod): array => [
                'id' => $scoredPeriod['period']->getKey(),
                'name' => $scoredPeriod['period']->name,
                'start_date' => $scoredPeriod['period']->start_date->toDateString(),
                'end_date' => $scoredPeriod['period']->end_date->toDateString(),
            ])->values(),
            'groups' => [
                'ikhwan' => $this->annualMatrixRows($participantGroups['ikhwan']),
                'akhwat' => $this->annualMatrixRows($participantGroups['akhwat']),
                'leader' => $this->annualMatrixRows($participantGroups['leader']),
                'department' => $this->annualMatrixRows($departments),
            ],
        ];
    }

    /** @param array<string, array{name: string, department?: string, cells: array<string, float>}> $rows */
    private function annualMatrixRows(array $rows): array
    {
        $rank = 0;
        $previousAverage = null;

        return collect($rows)
            ->map(function (array $row): array {
                $row['period_count'] = count($row['cells']);
                $row['average_percentage'] = round(array_sum($row['cells']) / $row['period_count'], 2);

                return $row;
            })
            ->sort(function (array $left, array $right): int {
                return $right['average_percentage'] <=> $left['average_percentage']
                    ?: strcasecmp($left['name'], $right['name']);
            })
            ->values()
            ->map(function (array $row, int $index) use (&$rank, &$previousAverage): array {
                if ($previousAverage === null || $row['average_percentage'] !== $previousAverage) {
                    $rank = $index + 1;
                    $previousAverage = $row['average_percentage'];
                }
                $row['rank'] = $rank;

                return $row;
            })
            ->all();
    }
}
