<?php

namespace App\Http\Controllers\Lks;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Department;
use App\Models\PeriodParticipantSnapshot;
use App\Models\Role;
use App\Models\SantriProfile;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class OrganizationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->ensureAdmin($request);
        $search = trim((string) $request->query('people_search'));
        $santriQuery = SantriProfile::query()
            ->with(['user:id,name,email,is_active', 'user.roles:id,code', 'department:id,name', 'team:id,name,leader_user_id', 'team.leader:id,name'])
            ->when($search !== '', fn ($query) => $query->whereHas('user', fn ($userQuery) => $userQuery
                ->where('name', 'ilike', "%{$search}%")
                ->orWhere('email', 'ilike', "%{$search}%")))
            ->orderBy('created_at', 'desc');
        $santri = $santriQuery->paginate(25, ['*'], 'people_page');
        $organizationSearch = trim((string) $request->query('organization_search'));
        $departments = Department::query()
            ->with(['teams' => fn ($query) => $query->with(['leader:id,name'])->withCount('santriProfiles')->orderBy('name')])
            ->withCount('santriProfiles')
            ->when($organizationSearch !== '', fn ($query) => $query->where(fn ($organizationQuery) => $organizationQuery
                ->where('name', 'ilike', "%{$organizationSearch}%")
                ->orWhere('code', 'ilike', "%{$organizationSearch}%")
                ->orWhereHas('teams', fn ($teamQuery) => $teamQuery
                    ->where('name', 'ilike', "%{$organizationSearch}%")
                    ->orWhere('code', 'ilike', "%{$organizationSearch}%"))))
            ->orderBy('name')
            ->paginate(25, ['*'], 'organization_page');

        return response()->json(['data' => [
            'departments' => $departments->items(),
            'department_pagination' => $this->pagination($departments),
            'department_options' => Department::query()->with(['teams' => fn ($query) => $query
                ->with('leader:id,name')
                ->withCount(['santriProfiles as active_santri_profiles_count' => fn ($profiles) => $profiles->where('status', 'active')])
                ->select('id', 'department_id', 'leader_user_id', 'code', 'name', 'is_active')])->orderBy('name')->get(),
            'team_leader_options' => SantriProfile::query()->where('status', 'active')->where('level', 'leader')->with('user:id,name')->orderBy('created_at')->get(['user_id', 'team_id']),
            'santri' => $santri->items(),
            'santri_pagination' => $this->pagination($santri),
            'santri_options' => SantriProfile::query()->where('status', 'active')->with('user:id,name')->orderBy('created_at', 'desc')->get(),
        ]]);
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

    public function storeDepartment(Request $request): JsonResponse
    {
        $this->ensureAdmin($request);
        $department = Department::create($request->validate([
            'code' => ['required', 'string', 'max:30', 'unique:departments,code'],
            'name' => ['required', 'string', 'max:100'],
        ]));

        return response()->json(['data' => $department], 201);
    }

    public function storeTeam(Request $request): JsonResponse
    {
        $this->ensureAdmin($request);
        $team = Team::create($request->validate([
            'department_id' => ['required', 'uuid', 'exists:departments,id'],
            'code' => ['required', 'string', 'max:30', 'unique:teams,code'],
            'name' => ['required', 'string', 'max:100'],
        ]));

        return response()->json(['data' => $team], 201);
    }

    public function updateDepartment(Request $request, Department $department): JsonResponse
    {
        $this->ensureAdmin($request);
        $wasActive = $department->is_active;
        $data = $request->validate([
            'code' => ['sometimes', 'required', 'string', 'max:30', Rule::unique('departments', 'code')->ignore($department)],
            'name' => ['sometimes', 'required', 'string', 'max:100'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if (($data['is_active'] ?? true) === false && $department->teams()->where('is_active', true)->exists()) {
            throw ValidationException::withMessages(['is_active' => 'Arsipkan atau nonaktifkan seluruh tim aktif di departemen ini terlebih dahulu.']);
        }

        $department->update($data);
        if (array_key_exists('is_active', $data) && $wasActive !== $department->is_active) {
            AuditLog::create(['actor_user_id' => $request->user()->getKey(), 'event' => $department->is_active ? 'department.reactivated' : 'department.archived', 'auditable_type' => $department->getMorphClass(), 'auditable_id' => $department->getKey(), 'before_data' => ['is_active' => $wasActive], 'after_data' => ['is_active' => $department->is_active]]);
        }

        return response()->json(['data' => $department->refresh()]);
    }

    public function updateTeam(Request $request, Team $team): JsonResponse
    {
        $this->ensureAdmin($request);
        $wasActive = $team->is_active;
        $data = $request->validate([
            'code' => ['sometimes', 'required', 'string', 'max:30', Rule::unique('teams', 'code')->ignore($team)],
            'name' => ['sometimes', 'required', 'string', 'max:100'],
            'leader_user_id' => ['nullable', 'uuid', 'exists:users,id'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if (($data['is_active'] ?? true) === false && $team->santriProfiles()->where('status', 'active')->exists()) {
            throw ValidationException::withMessages(['is_active' => 'Pindahkan atau nonaktifkan seluruh Santri Karya aktif di tim ini terlebih dahulu.']);
        }

        $previousLeader = $team->leader_user_id;
        if (array_key_exists('leader_user_id', $data)) {
            $this->ensureTeamLeader($team, $data['leader_user_id']);
        }
        $leaderChanged = array_key_exists('leader_user_id', $data) && $previousLeader !== $data['leader_user_id'];

        DB::transaction(function () use ($request, $team, $data, $leaderChanged, $previousLeader, $wasActive): void {
            $team->update($data);
            if ($leaderChanged) {
                $this->syncProfilesToTeamLeader($team);
                $activeParticipantCount = $this->syncActivePeriodParticipantsToTeamLeader($team);
                AuditLog::create(['actor_user_id' => $request->user()->getKey(), 'event' => 'team.leader_changed', 'auditable_type' => $team->getMorphClass(), 'auditable_id' => $team->getKey(), 'before_data' => ['leader_user_id' => $previousLeader], 'after_data' => ['leader_user_id' => $team->leader_user_id, 'active_participant_count' => $activeParticipantCount]]);
            }
            if (array_key_exists('is_active', $data) && $wasActive !== $team->is_active) {
                AuditLog::create(['actor_user_id' => $request->user()->getKey(), 'event' => $team->is_active ? 'team.reactivated' : 'team.archived', 'auditable_type' => $team->getMorphClass(), 'auditable_id' => $team->getKey(), 'before_data' => ['is_active' => $wasActive], 'after_data' => ['is_active' => $team->is_active]]);
            }
        });

        return response()->json(['data' => $team->refresh()]);
    }

    public function destroyDepartment(Request $request, Department $department): Response
    {
        $this->ensureAdmin($request);
        if ($department->teams()->exists() || $department->santriProfiles()->exists()
            || PeriodParticipantSnapshot::query()->where('department_id_snapshot', $department->getKey())->exists()) {
            throw ValidationException::withMessages(['department' => 'Departemen yang sudah memiliki tim, Santri Karya, atau riwayat periode tidak dapat dihapus. Arsipkan departemen ini sebagai gantinya.']);
        }

        $before = $department->only(['id', 'code', 'name']);
        DB::transaction(function () use ($request, $department, $before): void {
            AuditLog::create(['actor_user_id' => $request->user()->getKey(), 'event' => 'department.deleted', 'auditable_type' => $department->getMorphClass(), 'auditable_id' => $department->getKey(), 'before_data' => $before]);
            $department->delete();
        });

        return response()->noContent();
    }

    public function destroyTeam(Request $request, Team $team): Response
    {
        $this->ensureAdmin($request);
        if ($team->santriProfiles()->exists()
            || PeriodParticipantSnapshot::query()->where('team_id_snapshot', $team->getKey())->exists()) {
            throw ValidationException::withMessages(['team' => 'Tim yang sudah memiliki Santri Karya atau riwayat periode tidak dapat dihapus. Arsipkan tim ini sebagai gantinya.']);
        }

        $before = $team->only(['id', 'code', 'name', 'department_id']);
        DB::transaction(function () use ($request, $team, $before): void {
            AuditLog::create(['actor_user_id' => $request->user()->getKey(), 'event' => 'team.deleted', 'auditable_type' => $team->getMorphClass(), 'auditable_id' => $team->getKey(), 'before_data' => $before]);
            $team->delete();
        });

        return response()->noContent();
    }

    public function storeSantri(Request $request): JsonResponse
    {
        $this->ensureAdmin($request);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'temporary_password' => ['required', 'string', 'min:8', 'max:255'],
            'gender' => ['required', 'in:ikhwan,akhwat'],
            'team_id' => ['required', 'uuid', 'exists:teams,id'],
            'level' => ['required', Rule::in(['staff', 'leader'])],
        ]);

        $team = Team::query()->findOrFail($data['team_id']);
        $roleIds = $this->requiredRoleIds(['santri', 'leader']);

        $profile = DB::transaction(function () use ($data, $team, $roleIds): SantriProfile {
            $user = new User([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['temporary_password'],
            ]);
            $user->email_verified_at = now();
            $user->must_change_password = true;
            $user->save();

            $user->roles()->sync($data['level'] === 'leader'
                ? [$roleIds['santri'], $roleIds['leader']]
                : [$roleIds['santri']]);

            return SantriProfile::create([
                'user_id' => $user->getKey(),
                'gender' => $data['gender'],
                'department_id' => $team->department_id,
                'team_id' => $team->getKey(),
                'leader_user_id' => $team->leader_user_id,
                'level' => $data['level'],
                'status' => 'active',
            ]);
        });

        return response()->json(['data' => $profile->load(['user:id,name,email', 'department:id,name', 'team:id,name', 'leader:id,name'])], 201);
    }

    public function updateSantri(Request $request, SantriProfile $profile): JsonResponse
    {
        $this->ensureAdmin($request);
        $previousStatus = $profile->status;
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($profile->getKey())],
            'gender' => ['required', 'in:ikhwan,akhwat'],
            'team_id' => ['required', 'uuid', 'exists:teams,id'],
            'level' => ['required', Rule::in(['staff', 'leader'])],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $team = Team::query()->where('is_active', true)->find($data['team_id']);
        if ($team === null) {
            throw ValidationException::withMessages(['team_id' => 'Pilih tim yang masih aktif untuk penempatan Santri Karya.']);
        }

        if ($data['level'] === 'staff' && Team::query()->where('leader_user_id', $profile->getKey())->exists()) {
            throw ValidationException::withMessages(['level' => 'Pilih Leader Tim pengganti sebelum mengubah level jabatan menjadi Staff.']);
        }

        if ($data['status'] === 'inactive' && Team::query()->where('leader_user_id', $profile->getKey())->exists()) {
            throw ValidationException::withMessages(['status' => 'Pilih Leader Tim pengganti sebelum menonaktifkan akun ini.']);
        }

        if (Team::query()->where('leader_user_id', $profile->getKey())->where('id', '!=', $team->getKey())->exists()) {
            throw ValidationException::withMessages(['team_id' => 'Pilih Leader Tim pengganti sebelum memindahkan akun ini ke tim lain.']);
        }

        $roleIds = $this->requiredRoleIds(['santri', 'leader']);

        $profile = DB::transaction(function () use ($data, $team, $profile, $roleIds): SantriProfile {
            $profile->load('user');
            $profile->user->name = $data['name'];
            $profile->user->email = $data['email'];
            $profile->user->is_active = $data['status'] === 'active';
            $profile->user->save();

            $profile->update([
                'gender' => $data['gender'],
                'department_id' => $team->department_id,
                'team_id' => $team->getKey(),
                'leader_user_id' => $team->leader_user_id === $profile->getKey() ? null : $team->leader_user_id,
                'level' => $data['level'],
                'status' => $data['status'],
            ]);

            $profile->user->roles()->syncWithoutDetaching([$roleIds['santri']]);

            if ($data['level'] === 'leader') {
                $profile->user->roles()->syncWithoutDetaching([$roleIds['leader']]);
            } else {
                $profile->user->roles()->detach($roleIds['leader']);
            }

            return $profile->refresh();
        });
        if ($previousStatus !== $profile->status) {
            AuditLog::create(['actor_user_id' => $request->user()->getKey(), 'event' => $profile->status === 'active' ? 'santri.reactivated' : 'santri.deactivated', 'auditable_type' => $profile->getMorphClass(), 'auditable_id' => $profile->getKey(), 'before_data' => ['status' => $previousStatus], 'after_data' => ['status' => $profile->status]]);
        }

        return response()->json(['data' => $profile->load(['user:id,name,email,is_active', 'user.roles:id,code', 'department:id,name', 'team:id,name', 'leader:id,name'])]);
    }

    public function destroySantri(Request $request, User $user): Response
    {
        $this->ensureAdmin($request);
        $profile = SantriProfile::query()->where('user_id', $user->getKey())->first();
        if ($profile === null) {
            throw ValidationException::withMessages(['santri' => 'Profil Santri Karya untuk akun ini sudah tidak tersedia. Muat ulang data dan coba kembali.']);
        }
        if ($user->getKey() === $request->user()->getKey() || $user->isAdmin()) {
            throw ValidationException::withMessages(['santri' => 'Akun Admin tidak dapat dihapus dari Pengaturan Santri Karya.']);
        }
        if (PeriodParticipantSnapshot::query()->where('user_id', $user->getKey())->exists()) {
            throw ValidationException::withMessages(['santri' => 'Santri Karya yang sudah memiliki riwayat periode tidak dapat dihapus. Nonaktifkan akun ini sebagai gantinya.']);
        }
        if (Team::query()->where('leader_user_id', $user->getKey())->exists()) {
            throw ValidationException::withMessages(['santri' => 'Pilih Leader Tim pengganti sebelum menghapus akun ini.']);
        }

        $before = ['user_id' => $user->getKey(), 'name' => $user->name, 'email' => $user->email];
        DB::transaction(function () use ($request, $profile, $user, $before): void {
            AuditLog::create(['actor_user_id' => $request->user()->getKey(), 'event' => 'santri.deleted', 'auditable_type' => $profile->getMorphClass(), 'auditable_id' => $profile->getKey(), 'before_data' => $before]);
            $user->delete();
        });

        return response()->noContent();
    }

    /** @param array<int, string> $roleCodes
     *  @return array<string, string>
     */
    private function requiredRoleIds(array $roleCodes): array
    {
        $roleNames = ['admin' => 'Admin', 'leader' => 'Leader', 'santri' => 'Santri Karya'];
        $roleIds = [];

        foreach ($roleCodes as $code) {
            $roleIds[$code] = Role::query()->firstOrCreate(
                ['code' => $code],
                ['name' => $roleNames[$code]],
            )->getKey();
        }

        return $roleIds;
    }

    private function ensureAdmin(Request $request): void
    {
        abort_unless($request->user()?->isAdmin(), 403);
    }

    private function ensureTeamLeader(Team $team, ?string $leaderUserId): void
    {
        if ($leaderUserId === null) {
            return;
        }

        $isEligible = $team->santriProfiles()
            ->where('user_id', $leaderUserId)
            ->where('status', 'active')
            ->where('level', 'leader')
            ->exists();

        if (! $isEligible) {
            throw ValidationException::withMessages(['leader_user_id' => 'Pilih Santri Karya aktif dengan level jabatan Leader dari tim ini.']);
        }
    }

    private function syncProfilesToTeamLeader(Team $team): void
    {
        $profiles = $team->santriProfiles();
        if ($team->leader_user_id === null) {
            $profiles->update(['leader_user_id' => null]);
            return;
        }

        $profiles->where('user_id', '!=', $team->leader_user_id)->update(['leader_user_id' => $team->leader_user_id]);
        $team->santriProfiles()->where('user_id', $team->leader_user_id)->update(['leader_user_id' => null]);
    }

    private function syncActivePeriodParticipantsToTeamLeader(Team $team): int
    {
        $team->load('leader:id,name');

        return PeriodParticipantSnapshot::query()
            ->where('team_id_snapshot', $team->getKey())
            ->whereHas('period', fn ($period) => $period->where('status', 'active'))
            ->update([
                'leader_user_id_snapshot' => $team->leader?->getKey(),
                'leader_name_snapshot' => $team->leader?->name,
            ]);
    }
}
