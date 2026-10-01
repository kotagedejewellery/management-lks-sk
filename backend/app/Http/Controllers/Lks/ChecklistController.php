<?php

namespace App\Http\Controllers\Lks;

use App\Http\Controllers\Controller;
use App\Http\Requests\Lks\StoreChecklistRequest;
use App\Models\LksPeriod;
use App\Models\PeriodActivity;
use App\Models\PeriodParticipantSnapshot;
use App\Services\ChecklistRecordingService;
use Illuminate\Http\JsonResponse;

class ChecklistController extends Controller
{
    public function store(StoreChecklistRequest $request, ChecklistRecordingService $service): JsonResponse
    {
        $data = $request->validated();
        $actor = $request->user();
        $activity = PeriodActivity::query()->findOrFail($data['period_activity_id']);

        $participant = isset($data['participant_id'])
            ? PeriodParticipantSnapshot::query()->findOrFail($data['participant_id'])
            : LksPeriod::query()
                ->where('status', 'active')
                ->firstOrFail()
                ->participants()
                ->where('user_id', $actor->getKey())
                ->firstOrFail();

        $checklist = $service->record(
            $actor,
            $participant,
            $activity,
            $data['checklist_date'],
            $request->boolean('is_completed'),
            $data['reason'] ?? null,
        );

        return response()->json(['data' => [
            'id' => $checklist->getKey(),
            'period_activity_id' => $checklist->period_activity_id,
            'checklist_date' => $checklist->checklist_date->toDateString(),
            'is_completed' => $checklist->is_completed,
        ]]);
    }
}
