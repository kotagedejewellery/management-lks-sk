<?php

namespace App\Http\Controllers\Lks;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\LksActivity;
use App\Models\LksPeriod;
use App\Models\PeriodActivity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PeriodConfigurationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->ensureAdmin($request);
        $periodSearch = trim((string) $request->query('period_search', ''));
        $activitySearch = trim((string) $request->query('activity_search', ''));
        $periods = LksPeriod::query()
            ->with('periodActivities')
            ->when($periodSearch !== '', fn ($query) => $query->where(function ($query) use ($periodSearch) {
                $query->where('name', 'ilike', "%{$periodSearch}%")
                    ->orWhere('status', 'ilike', "%{$periodSearch}%");
            }))
            ->latest('start_date')
            ->paginate(25, ['*'], 'period_page');
        $activities = LksActivity::query()
            ->with(['periodActivities.period'])
            ->when($activitySearch !== '', fn ($query) => $query->where(function ($query) use ($activitySearch) {
                $query->where('name', 'ilike', "%{$activitySearch}%")
                    ->orWhere('code', 'ilike', "%{$activitySearch}%");
            }))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(25, ['*'], 'activity_page');

        return response()->json(['data' => [
            'periods' => $periods->items(),
            'period_pagination' => $this->pagination($periods),
            'period_options' => LksPeriod::query()->with('periodActivities')->latest('start_date')->get(),
            'activities' => $activities->items(),
            'activity_pagination' => $this->pagination($activities),
            'activity_options' => LksActivity::query()->orderBy('sort_order')->orderBy('name')->get(),
        ]]);
    }

    public function storePeriod(Request $request): JsonResponse
    {
        $this->ensureAdmin($request);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'final_passing_threshold' => ['nullable', 'numeric', 'between:0,100'],
        ]);
        $data['created_by'] = $request->user()->getKey();

        return response()->json(['data' => LksPeriod::create($data)], 201);
    }

    public function storeActivity(Request $request): JsonResponse
    {
        $this->ensureAdmin($request);
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:lks_activities,code'],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
        ]);
        $data['sort_order'] = ((int) LksActivity::query()->max('sort_order')) + 1;
        $activity = LksActivity::create($data);

        return response()->json(['data' => $activity], 201);
    }

    public function updatePeriod(Request $request, LksPeriod $period): JsonResponse
    {
        $this->ensureAdmin($request);
        if ($period->status !== 'draft') {
            throw ValidationException::withMessages(['period' => 'Hanya periode draft yang dapat diubah.']);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'final_passing_threshold' => ['required', 'numeric', 'between:0,100'],
        ]);
        $period->update($data);

        return response()->json(['data' => $period->refresh()->load('periodActivities')]);
    }

    public function updateActivity(Request $request, LksActivity $activity): JsonResponse
    {
        $this->ensureAdmin($request);
        $wasActive = $activity->is_active;
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('lks_activities', 'code')->ignore($activity)],
            'name' => ['required', 'string', 'max:150'],
            'is_active' => ['required', 'boolean'],
        ]);
        $activity->update($data);
        if ($wasActive !== $activity->is_active) {
            AuditLog::create(['actor_user_id' => $request->user()->getKey(), 'event' => $activity->is_active ? 'activity.reactivated' : 'activity.deactivated', 'auditable_type' => $activity->getMorphClass(), 'auditable_id' => $activity->getKey(), 'before_data' => ['is_active' => $wasActive], 'after_data' => ['is_active' => $activity->is_active]]);
        }

        return response()->json(['data' => $activity->refresh()]);
    }

    public function destroyPeriod(Request $request, LksPeriod $period): Response
    {
        $this->ensureAdmin($request);
        if ($period->status !== 'draft') {
            throw ValidationException::withMessages(['period' => 'Hanya periode draft yang dapat dihapus. Periode aktif atau tertutup disimpan sebagai riwayat.']);
        }
        if ($period->participants()->exists() || $period->periodActivities()->whereHas('checklists')->exists()) {
            throw ValidationException::withMessages(['period' => 'Periode yang sudah memiliki peserta atau checklist tidak dapat dihapus.']);
        }

        $before = $period->only(['id', 'name', 'start_date', 'end_date', 'status']);
        DB::transaction(function () use ($request, $period, $before): void {
            $period->periodActivities()->delete();
            AuditLog::create(['actor_user_id' => $request->user()->getKey(), 'event' => 'period.deleted', 'auditable_type' => $period->getMorphClass(), 'auditable_id' => $period->getKey(), 'before_data' => $before]);
            $period->delete();
        });

        return response()->noContent();
    }

    public function destroyActivity(Request $request, LksActivity $activity): Response
    {
        $this->ensureAdmin($request);
        if ($activity->periodActivities()->exists()) {
            throw ValidationException::withMessages(['activity' => 'Aktivitas yang sudah dipakai dalam periode tidak dapat dihapus. Nonaktifkan aktivitas ini sebagai gantinya.']);
        }

        $before = $activity->only(['id', 'code', 'name']);
        DB::transaction(function () use ($request, $activity, $before): void {
            AuditLog::create(['actor_user_id' => $request->user()->getKey(), 'event' => 'activity.deleted', 'auditable_type' => $activity->getMorphClass(), 'auditable_id' => $activity->getKey(), 'before_data' => $before]);
            $activity->delete();
        });

        return response()->noContent();
    }

    public function storePeriodActivity(Request $request, LksPeriod $period): JsonResponse
    {
        $this->ensureAdmin($request);
        if ($period->status !== 'draft') {
            throw ValidationException::withMessages(['period' => 'Aktivitas hanya dapat diatur pada periode draft.']);
        }

        $data = $request->validate([
            'activity_id' => ['required', 'uuid', 'exists:lks_activities,id'],
            'target_count' => ['required', 'integer', 'min:1'],
            'allowed_weekdays' => ['nullable', 'array'],
            'allowed_weekdays.*' => ['integer', 'between:1,7', 'distinct'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:32767'],
        ]);
        $activity = LksActivity::query()->findOrFail($data['activity_id']);

        if ($period->periodActivities()->where('activity_code_snapshot', $activity->code)->exists()) {
            throw ValidationException::withMessages(['activity_id' => 'Aktivitas ini sudah ada pada periode.']);
        }

        $periodActivity = PeriodActivity::create([
            'period_id' => $period->getKey(),
            'activity_id' => $activity->getKey(),
            'activity_code_snapshot' => $activity->code,
            'activity_name_snapshot' => $activity->name,
            'target_count' => $data['target_count'],
            'allowed_weekdays' => isset($data['allowed_weekdays'])
                ? '{'.implode(',', $data['allowed_weekdays']).'}'
                : null,
            'weight' => 1,
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        return response()->json(['data' => $periodActivity], 201);
    }

    public function updatePeriodActivities(Request $request, LksPeriod $period): JsonResponse
    {
        $this->ensureAdmin($request);
        if ($period->status !== 'draft') {
            throw ValidationException::withMessages(['period' => 'Aktivitas hanya dapat diatur pada periode draft.']);
        }

        $data = $request->validate([
            'activities' => ['required', 'array', 'min:1'],
            'activities.*.id' => ['required', 'uuid', 'distinct'],
            'activities.*.target_count' => ['required', 'integer', 'min:1'],
            'activities.*.is_active' => ['required', 'boolean'],
            'activities.*.allowed_weekdays' => ['nullable', 'array'],
            'activities.*.allowed_weekdays.*' => ['integer', 'between:1,7', 'distinct'],
        ]);

        $periodActivities = $period->periodActivities()->whereIn('id', collect($data['activities'])->pluck('id'))->get()->keyBy('id');
        if ($periodActivities->count() !== count($data['activities'])) {
            throw ValidationException::withMessages(['activities' => 'Satu atau lebih aktivitas tidak termasuk dalam periode ini.']);
        }
        $before = $periodActivities->map(fn (PeriodActivity $activity) => [
            'id' => $activity->getKey(),
            'target_count' => $activity->target_count,
            'is_active' => $activity->is_active,
            'allowed_weekdays' => $activity->allowedWeekdays(),
        ])->values()->all();

        foreach ($data['activities'] as $activity) {
            $periodActivities[$activity['id']]->update([
                'target_count' => $activity['target_count'],
                'is_active' => $activity['is_active'],
                'allowed_weekdays' => isset($activity['allowed_weekdays'])
                    ? '{'.implode(',', $activity['allowed_weekdays']).'}'
                    : null,
            ]);
        }

        AuditLog::create([
            'actor_user_id' => $request->user()->getKey(),
            'event' => 'period.activities.updated',
            'auditable_type' => $period->getMorphClass(),
            'auditable_id' => $period->getKey(),
            'before_data' => ['activities' => $before],
            'after_data' => ['activities' => $data['activities']],
        ]);

        return response()->json(['data' => $period->fresh()->load('periodActivities')]);
    }

    /** @return array<string, int> */
    private function pagination($paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
        ];
    }

    private function ensureAdmin(Request $request): void
    {
        abort_unless($request->user()?->isAdmin(), 403);
    }
}
