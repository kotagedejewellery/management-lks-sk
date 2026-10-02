<?php

namespace App\Http\Controllers\Lks;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Role;
use App\Models\SantriProfile;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
            ->with(['user:id,name,email,is_active', 'user.roles:id,code', 'department:id,name', 'team:id,name', 'leader:id,name'])
            ->when($search !== '', fn ($query) => $query->whereHas('user', fn ($userQuery) => $userQuery
                ->where('name', 'ilike', "%{$search}%")
                ->orWhere('email', 'ilike', "%{$search}%")))
            ->orderBy('created_at', 'desc');
        $santri = $santriQuery->paginate(25, ['*'], 'people_page');
        $organizationSearch = trim((string) $request->query('organization_search'));
        $departments = Department::query()
            ->with(['teams' => fn ($query) => $query->withCount('santriProfiles')->orderBy('name')])
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
            'department_options' => Department::query()->with('teams:id,department_id,code,name,is_active')->orderBy('name')->get(),
            'leaders' => User::query()->whereHas('roles', fn ($query) => $query->where('code', 'leader'))->orderBy('name')->get(['id', 'name']),
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
        $data = $request->validate([
            'code' => ['sometimes', 'required', 'string', 'max:30', Rule::unique('departments', 'code')->ignore($department)],
            'name' => ['sometimes', 'required', 'string', 'max:100'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if (($data['is_active'] ?? true) === false && $department->teams()->where('is_active', true)->exists()) {
            throw ValidationException::withMessages(['is_active' => 'Arsipkan atau nonaktifkan seluruh tim aktif di departemen ini terlebih dahulu.']);
        }

        $department->update($data);

        return response()->json(['data' => $department->refresh()]);
    }

    public function updateTeam(Request $request, Team $team): JsonResponse
    {
        $this->ensureAdmin($request);
        $data = $request->validate([
            'code' => ['sometimes', 'required', 'string', 'max:30', Rule::unique('teams', 'code')->ignore($team)],
            'name' => ['sometimes', 'required', 'string', 'max:100'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if (($data['is_active'] ?? true) === false && $team->santriProfiles()->where('status', 'active')->exists()) {
            throw ValidationException::withMessages(['is_active' => 'Pindahkan atau nonaktifkan seluruh Santri Karya aktif di tim ini terlebih dahulu.']);
        }

        $team->update($data);

        return response()->json(['data' => $team->refresh()]);
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
            'leader_user_id' => ['nullable', 'uuid', 'exists:users,id'],
            'category' => ['nullable', 'string', 'max:100'],
            'level' => ['nullable', 'string', 'max:100'],
            'is_leader' => ['nullable', 'boolean'],
        ]);
        $data['is_leader'] = $request->boolean('is_leader');

        $team = Team::query()->findOrFail($data['team_id']);
        if (isset($data['leader_user_id']) && ! User::query()->whereKey($data['leader_user_id'])
            ->whereHas('roles', fn ($query) => $query->where('code', 'leader'))->exists()) {
            throw ValidationException::withMessages(['leader_user_id' => 'Pengguna yang dipilih belum memiliki role Leader.']);
        }

        $roleCodes = ['santri'];
        if ($data['is_leader']) {
            $roleCodes[] = 'leader';
        }
        $roleIds = $this->requiredRoleIds($roleCodes);

        $profile = DB::transaction(function () use ($data, $team, $roleIds): SantriProfile {
            $user = new User([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['temporary_password'],
            ]);
            $user->email_verified_at = now();
            $user->save();

            $user->roles()->sync(array_values($roleIds));

            return SantriProfile::create([
                'user_id' => $user->getKey(),
                'gender' => $data['gender'],
                'department_id' => $team->department_id,
                'team_id' => $team->getKey(),
                'leader_user_id' => $data['leader_user_id'] ?? null,
                'category' => $data['category'] ?? null,
                'level' => $data['level'] ?? null,
                'status' => 'active',
            ]);
        });

        return response()->json(['data' => $profile->load(['user:id,name,email', 'department:id,name', 'team:id,name', 'leader:id,name'])], 201);
    }

    public function updateSantri(Request $request, SantriProfile $profile): JsonResponse
    {
        $this->ensureAdmin($request);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($profile->getKey())],
            'gender' => ['required', 'in:ikhwan,akhwat'],
            'team_id' => ['required', 'uuid', 'exists:teams,id'],
            'leader_user_id' => ['nullable', 'uuid', 'exists:users,id'],
            'category' => ['nullable', 'string', 'max:100'],
            'level' => ['nullable', 'string', 'max:100'],
            'is_leader' => ['required', 'boolean'],
            'status' => ['required', 'in:active,inactive'],
        ]);
        $data['is_leader'] = $request->boolean('is_leader');

        $team = Team::query()->where('is_active', true)->find($data['team_id']);
        if ($team === null) {
            throw ValidationException::withMessages(['team_id' => 'Pilih tim yang masih aktif untuk penempatan Santri Karya.']);
        }

        if (($data['leader_user_id'] ?? null) === $profile->getKey()) {
            throw ValidationException::withMessages(['leader_user_id' => 'Santri Karya tidak dapat menjadi leader untuk dirinya sendiri.']);
        }

        if (isset($data['leader_user_id']) && ! User::query()->whereKey($data['leader_user_id'])
            ->whereHas('roles', fn ($query) => $query->where('code', 'leader'))->exists()) {
            throw ValidationException::withMessages(['leader_user_id' => 'Pengguna yang dipilih belum memiliki role Leader.']);
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
                'leader_user_id' => $data['leader_user_id'] ?? null,
                'category' => $data['category'] ?? null,
                'level' => $data['level'] ?? null,
                'status' => $data['status'],
            ]);

            $profile->user->roles()->syncWithoutDetaching([$roleIds['santri']]);

            if ($data['is_leader']) {
                $profile->user->roles()->syncWithoutDetaching([$roleIds['leader']]);
            } else {
                $profile->user->roles()->detach($roleIds['leader']);
            }

            return $profile->refresh();
        });

        return response()->json(['data' => $profile->load(['user:id,name,email,is_active', 'user.roles:id,code', 'department:id,name', 'team:id,name', 'leader:id,name'])]);
    }

    /** @param array<int, string> $roleCodes
     *  @return array<string, string>
     */
    private function requiredRoleIds(array $roleCodes): array
    {
        $roleIds = Role::query()->whereIn('code', $roleCodes)->pluck('id', 'code')->all();
        $missingRoles = array_values(array_diff($roleCodes, array_keys($roleIds)));

        if ($missingRoles !== []) {
            throw ValidationException::withMessages([
                'roles' => 'Konfigurasi role '.implode(', ', $missingRoles).' belum tersedia. Jalankan provisioning database sebelum mengelola Santri Karya.',
            ]);
        }

        return $roleIds;
    }

    private function ensureAdmin(Request $request): void
    {
        abort_unless($request->user()?->isAdmin(), 403);
    }
}
