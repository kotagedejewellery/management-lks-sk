<?php

namespace App\Http\Controllers\Lks;

use App\Http\Controllers\Controller;
use App\Http\Requests\Lks\ActivatePeriodRequest;
use App\Http\Requests\Lks\AddPeriodParticipantRequest;
use App\Models\LksPeriod;
use App\Models\SantriProfile;
use App\Services\PeriodActivationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        $periods = LksPeriod::query()
            ->where('status', 'closed')
            ->when(! $viewer->isAdmin() && $viewer->hasRole('leader'), fn ($query) => $query->whereHas(
                'participants',
                fn ($participants) => $participants->where('leader_user_id_snapshot', $viewer->getKey()),
            ))
            ->when(! $viewer->isAdmin() && ! $viewer->hasRole('leader'), fn ($query) => $query->whereHas(
                'participants',
                fn ($participants) => $participants->where('user_id', $viewer->getKey()),
            ))
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
