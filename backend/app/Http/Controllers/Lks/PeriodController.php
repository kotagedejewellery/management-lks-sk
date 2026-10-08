<?php

namespace App\Http\Controllers\Lks;

use App\Http\Controllers\Controller;
use App\Http\Requests\Lks\ActivatePeriodRequest;
use App\Http\Requests\Lks\AddPeriodParticipantRequest;
use App\Models\LksPeriod;
use App\Models\PeriodParticipantSnapshot;
use App\Models\SantriProfile;
use App\Services\PeriodActivationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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

        $search = trim((string) $request->query('search', ''));
        $participants = $period->participants()
            ->withCount('checklists')
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('participant_name_snapshot', 'ilike', "%{$search}%")
                    ->orWhere('department_name_snapshot', 'ilike', "%{$search}%")
                    ->orWhere('team_name_snapshot', 'ilike', "%{$search}%");
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

    public function history(Request $request): JsonResponse
    {
        $viewer = $request->user();
        $personalScope = $request->query('scope') === 'personal';
        $periods = LksPeriod::query()
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
            )
            ->orderByDesc('end_date')
            ->get()
            ->map(fn (LksPeriod $period) => [
                'id' => $period->getKey(),
                'name' => $period->name,
                'start_date' => $period->start_date->toDateString(),
                'end_date' => $period->end_date->toDateString(),
                'closed_at' => $period->closed_at?->toIso8601String(),
            ]);

        return response()->json(['data' => $periods]);
    }
}
