<?php

namespace Tests\Feature\Lks;

use App\Models\Department;
use App\Models\DocumentSignatory;
use App\Models\LksActivity;
use App\Models\LksDocumentVerification;
use App\Models\LksChecklist;
use App\Models\LksPeriod;
use App\Models\PeriodActivity;
use App\Models\PeriodHolidaySnapshot;
use App\Models\PeriodParticipantSnapshot;
use App\Models\Role;
use App\Models\SantriProfile;
use App\Models\Team;
use App\Models\User;
use App\Services\LksScoreCalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LksCoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_close_an_active_period_before_its_end_date(): void
    {
        $admin = $this->userWithRole('admin');
        $period = $this->period($admin, ['status' => 'active', 'end_date' => now()->addWeek()->toDateString()]);

        $this->actingAs($admin)
            ->postJson(route('api.lks.periods.close', $period))
            ->assertOk()
            ->assertJsonPath('data.status', 'closed');

        $this->assertDatabaseHas('lks_periods', ['id' => $period->id, 'status' => 'closed']);
        $this->assertDatabaseHas('audit_logs', [
            'actor_user_id' => $admin->id,
            'event' => 'period.closed',
            'auditable_id' => $period->id,
        ]);
    }

    public function test_admin_can_pause_and_resume_recording_without_changing_an_active_period_lifecycle(): void
    {
        $admin = $this->userWithRole('admin');
        $period = $this->period($admin, ['status' => 'active']);

        $this->actingAs($admin)
            ->patchJson(route('api.lks.periods.recording.update', $period), ['recording_enabled' => false])
            ->assertOk()
            ->assertJsonPath('data.status', 'active')
            ->assertJsonPath('data.recording_enabled', false);

        $this->assertDatabaseHas('audit_logs', ['actor_user_id' => $admin->id, 'event' => 'period.recording_paused', 'auditable_id' => $period->id]);

        $this->actingAs($admin)
            ->patchJson(route('api.lks.periods.recording.update', $period), ['recording_enabled' => true])
            ->assertOk()
            ->assertJsonPath('data.recording_enabled', true);

        $this->assertDatabaseHas('audit_logs', ['actor_user_id' => $admin->id, 'event' => 'period.recording_resumed', 'auditable_id' => $period->id]);
    }

    public function test_only_admin_can_change_recording_status(): void
    {
        $admin = $this->userWithRole('admin');
        $period = $this->period($admin, ['status' => 'active']);

        foreach ([$this->userWithRole('leader'), $this->userWithRole('santri')] as $actor) {
            $this->actingAs($actor)
                ->patchJson(route('api.lks.periods.recording.update', $period), ['recording_enabled' => false])
                ->assertForbidden();
        }

        $this->assertDatabaseHas('lks_periods', ['id' => $period->id, 'recording_enabled' => true]);
        $this->assertDatabaseMissing('audit_logs', ['event' => 'period.recording_paused', 'auditable_id' => $period->id]);
    }

    public function test_closed_period_cannot_change_recording_status(): void
    {
        $admin = $this->userWithRole('admin');
        $period = $this->period($admin, ['status' => 'closed', 'recording_enabled' => false]);

        $this->actingAs($admin)
            ->patchJson(route('api.lks.periods.recording.update', $period), ['recording_enabled' => true])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('period');

        $this->assertDatabaseHas('lks_periods', ['id' => $period->id, 'recording_enabled' => false]);
        $this->assertDatabaseMissing('audit_logs', ['event' => 'period.recording_resumed', 'auditable_id' => $period->id]);
    }

    public function test_paused_recording_rejects_santri_checklist_writes(): void
    {
        $santri = $this->userWithRole('santri');
        $period = $this->period($santri, ['status' => 'active', 'recording_enabled' => false]);
        $activity = $this->periodActivity($period, 1);
        $this->participant($period, $santri);

        $this->actingAs($santri)
            ->putJson(route('api.lks.checklists.store'), [
                'period_activity_id' => $activity->id,
                'checklist_date' => now()->toDateString(),
                'is_completed' => true,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('checklist');

        $this->actingAs($santri)
            ->postJson(route('api.lks.checklists.bulk'), [
                'period_activity_id' => $activity->id,
                'checklist_dates' => [now()->toDateString()],
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('checklist');
    }

    public function test_santri_cannot_write_after_an_active_period_ends_but_admin_can_correct_its_history(): void
    {
        $admin = $this->userWithRole('admin');
        $santri = $this->userWithRole('santri');
        $period = $this->period($admin, [
            'status' => 'active',
            'start_date' => now()->subWeek()->toDateString(),
            'end_date' => now()->subDay()->toDateString(),
        ]);
        $activity = $this->periodActivity($period, 1);
        $participant = $this->participant($period, $santri);

        $payload = ['period_activity_id' => $activity->id, 'checklist_date' => $period->end_date->toDateString(), 'is_completed' => true];
        $this->actingAs($santri)->putJson(route('api.lks.checklists.store'), $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('checklist');

        $this->actingAs($admin)->putJson(route('api.lks.checklists.store'), $payload + [
            'participant_id' => $participant->id,
            'reason' => 'Koreksi administrasi setelah periode berakhir',
        ])->assertOk();
    }

    public function test_recommendation_is_specific_and_snapshotted_when_the_period_closes(): void
    {
        $admin = $this->userWithRole('admin');
        $santri = $this->userWithRole('santri');
        $period = $this->period($admin, ['status' => 'active', 'staff_passing_threshold' => 85]);
        $activity = $this->periodActivity($period, 4);
        $activity->update(['activity_name_snapshot' => 'Tahajud', 'minimum_target_count' => 3]);
        $participant = $this->participant($period, $santri);
        $this->checklist($participant, $activity, $santri, now()->subDay()->toDateString());

        $recommendation = app(LksScoreCalculator::class)->calculate($participant)['recommendation'];

        $this->assertSame('Nilai Anda 25% masih 60 poin dari ambang 85%. Prioritaskan: Tahajud.', $recommendation);

        $this->actingAs($admin)->postJson(route('api.lks.periods.close', $period))->assertOk();

        $this->assertDatabaseHas('period_participant_snapshots', [
            'id' => $participant->id,
            'recommendation_snapshot' => $recommendation,
        ]);
    }

    public function test_admin_can_activate_a_draft_period_and_snapshot_active_santri(): void
    {
        $this->configureActivationSignatories();
        $admin = $this->userWithRole('admin');
        $santri = $this->userWithRole('santri');
        $department = Department::query()->create(['code' => 'ENG', 'name' => 'Engineering', 'is_active' => true]);
        $team = Team::query()->create(['department_id' => $department->id, 'code' => 'WEB', 'name' => 'Web', 'is_active' => true]);
        SantriProfile::query()->create([
            'user_id' => $santri->id,
            'gender' => 'ikhwan',
            'department_id' => $department->id,
            'team_id' => $team->id,
            'level' => 'staff',
            'status' => 'active',
        ]);
        $period = $this->period($admin);
        $this->periodActivity($period, 1);

        $this->withSession(['_token' => 'test-token'])->withHeader('X-CSRF-TOKEN', 'test-token')->actingAs($admin)
            ->postJson(route('api.lks.periods.activate', $period))
            ->assertOk()
            ->assertJsonPath('data.status', 'active');

        $this->assertDatabaseHas('period_participant_snapshots', [
            'period_id' => $period->id,
            'user_id' => $santri->id,
            'department_id_snapshot' => $department->id,
            'team_id_snapshot' => $team->id,
        ]);
        $this->assertCount(2, $period->fresh()->signatories_snapshot);
    }

    public function test_magang_profile_is_snapshotted_and_uses_the_magang_activity_targets(): void
    {
        $this->configureActivationSignatories();
        $admin = $this->userWithRole('admin');
        $santri = $this->userWithRole('santri');
        $department = Department::query()->create(['code' => 'MKT', 'name' => 'Marketing', 'is_active' => true]);
        $team = Team::query()->create(['department_id' => $department->id, 'code' => 'DIG', 'name' => 'Digital', 'is_active' => true]);
        SantriProfile::query()->create([
            'user_id' => $santri->id,
            'gender' => 'ikhwan',
            'department_id' => $department->id,
            'team_id' => $team->id,
            'category' => 'magang',
            'level' => 'staff',
            'status' => 'active',
        ]);
        $period = $this->period($admin);
        $activity = $this->periodActivity($period, 20);
        $activity->update(['minimum_target_count' => 14, 'magang_target_count' => 15, 'magang_minimum_target_count' => 11]);

        $this->withSession(['_token' => 'test-token'])->withHeader('X-CSRF-TOKEN', 'test-token')->actingAs($admin)
            ->postJson(route('api.lks.periods.activate', $period))
            ->assertOk();

        $participant = $period->fresh()->participants()->where('user_id', $santri->id)->firstOrFail();
        $score = app(LksScoreCalculator::class)->calculate($participant);

        $this->assertSame('magang', $participant->category_snapshot);
        $this->assertSame(15, $score['activities'][0]['target_count']);
        $this->assertSame(11, $score['activities'][0]['minimum_target_count']);

        $this->actingAs($santri)
            ->getJson(route('api.lks.dashboard', ['date' => now()->toDateString()]))
            ->assertOk()
            ->assertJsonPath('data.participant.category', 'magang')
            ->assertJsonPath('data.activities.0.lks_category', 'magang')
            ->assertJsonPath('data.activities.0.target_count', 15)
            ->assertJsonPath('data.activities.0.minimum_target_count', 11);
    }

    public function test_puasa_senin_kamis_master_rule_applies_to_both_levels_once_per_week(): void
    {
        $activity = LksActivity::query()->where('code', 'PUASA_SENIN_KAMIS')->firstOrFail();

        $this->assertSame('Puasa Senin-Kamis', $activity->name);
        $this->assertSame(1, $activity->default_max_per_week);
        $this->assertSame('{1,4}', $activity->default_allowed_weekdays);
        $this->assertSame('{leader,staff}', $activity->default_applicable_levels);
    }

    public function test_admin_can_filter_santri_by_organization_status_level_and_search(): void
    {
        $admin = $this->userWithRole('admin');
        $department = Department::query()->create(['code' => 'MKT', 'name' => 'Marketing', 'is_active' => true]);
        $otherDepartment = Department::query()->create(['code' => 'OPS', 'name' => 'Operasional', 'is_active' => true]);
        $team = Team::query()->create(['department_id' => $department->id, 'code' => 'MKT-DIG', 'name' => 'Digital', 'is_active' => true]);
        $otherTeam = Team::query()->create(['department_id' => $otherDepartment->id, 'code' => 'OPS-GUD', 'name' => 'Gudang', 'is_active' => true]);
        $leader = $this->userWithRole('leader', ['name' => 'Rahmad Leader']);
        $team->update(['leader_user_id' => $leader->id]);
        $matchingUser = $this->userWithRole('santri', ['name' => 'Zhafron Firdaus']);
        $otherUser = $this->userWithRole('santri', ['name' => 'Anggota Operasional']);
        $inactiveUser = $this->userWithRole('santri', ['name' => 'Anggota Nonaktif']);

        SantriProfile::query()->create(['user_id' => $matchingUser->id, 'gender' => 'ikhwan', 'department_id' => $department->id, 'team_id' => $team->id, 'leader_user_id' => $leader->id, 'level' => 'staff', 'status' => 'active']);
        SantriProfile::query()->create(['user_id' => $otherUser->id, 'gender' => 'akhwat', 'department_id' => $otherDepartment->id, 'team_id' => $otherTeam->id, 'level' => 'staff', 'status' => 'active']);
        SantriProfile::query()->create(['user_id' => $inactiveUser->id, 'gender' => 'ikhwan', 'department_id' => $department->id, 'team_id' => $team->id, 'leader_user_id' => $leader->id, 'level' => 'staff', 'status' => 'inactive']);
        SantriProfile::query()->where('user_id', $matchingUser->id)->update(['category' => 'magang']);

        $this->actingAs($admin)
            ->getJson(route('api.lks.admin.organization.index', [
                'people_department_id' => $department->id,
                'people_team_id' => $team->id,
                'people_status' => 'active',
                'people_level' => 'staff',
                'people_category' => 'magang',
                'people_search' => 'Rahmad',
            ]))
            ->assertOk()
            ->assertJsonPath('data.santri_pagination.total', 1)
            ->assertJsonPath('data.santri.0.user.name', 'Zhafron Firdaus');
    }

    public function test_admin_can_filter_period_participants_by_placement_checklist_and_category(): void
    {
        $admin = $this->userWithRole('admin');
        $regularUser = $this->userWithRole('santri', ['name' => 'Staff Reguler']);
        $magangUser = $this->userWithRole('santri', ['name' => 'Staff Magang']);
        $department = Department::query()->create(['code' => 'MKT', 'name' => 'Marketing', 'is_active' => true]);
        $team = Team::query()->create(['department_id' => $department->id, 'code' => 'MKT-DIG', 'name' => 'Digital', 'is_active' => true]);
        $period = $this->period($admin, ['status' => 'active']);
        $activity = $this->periodActivity($period, 1);
        $regularParticipant = $this->participant($period, $regularUser);
        $magangParticipant = $this->participant($period, $magangUser);

        $regularParticipant->update([
            'department_id_snapshot' => $department->id,
            'department_name_snapshot' => $department->name,
            'team_id_snapshot' => $team->id,
            'team_name_snapshot' => $team->name,
            'category_snapshot' => 'regular',
        ]);
        $magangParticipant->update([
            'department_id_snapshot' => $department->id,
            'department_name_snapshot' => $department->name,
            'team_id_snapshot' => $team->id,
            'team_name_snapshot' => $team->name,
            'category_snapshot' => 'magang',
        ]);
        $this->checklist($magangParticipant, $activity, $magangUser, now()->toDateString());

        $this->actingAs($admin)
            ->getJson(route('api.lks.periods.participants.index', [
                'period' => $period,
                'department_id' => $department->id,
                'team_id' => $team->id,
                'checklist_status' => 'none',
                'category' => 'regular',
            ]))
            ->assertOk()
            ->assertJsonPath('data.pagination.total', 1)
            ->assertJsonPath('data.participants.0.id', $regularParticipant->id);

        $this->actingAs($admin)
            ->getJson(route('api.lks.periods.participants.index', [
                'period' => $period,
                'checklist_status' => 'recorded',
                'category' => 'magang',
            ]))
            ->assertOk()
            ->assertJsonPath('data.pagination.total', 1)
            ->assertJsonPath('data.participants.0.id', $magangParticipant->id);
    }

    public function test_creating_santri_uses_the_selected_team_as_the_single_placement_source(): void
    {
        $admin = $this->userWithRole('admin');
        $leader = $this->userWithRole('leader', ['name' => 'Leader Tim']);
        $department = Department::query()->create(['code' => 'OPS', 'name' => 'Operasional', 'is_active' => true]);
        $team = Team::query()->create(['department_id' => $department->id, 'code' => 'OPS-1', 'name' => 'Operasional Satu', 'leader_user_id' => $leader->id, 'is_active' => true]);
        SantriProfile::query()->create(['user_id' => $leader->id, 'gender' => 'ikhwan', 'department_id' => $department->id, 'team_id' => $team->id, 'level' => 'leader', 'status' => 'active']);

        $this->withSession(['_token' => 'test-token'])->withHeader('X-CSRF-TOKEN', 'test-token')->actingAs($admin)
            ->postJson(route('api.lks.admin.santri.store'), [
                'name' => 'Anggota Baru',
                'email' => 'anggota-baru@example.test',
                'temporary_password' => 'passwordsementara123',
                'gender' => 'akhwat',
                'team_id' => $team->id,
                'level' => 'staff',
            ])
            ->assertCreated();

        $newUser = User::query()->where('email', 'anggota-baru@example.test')->firstOrFail();
        $this->assertDatabaseHas('santri_profiles', [
            'user_id' => $newUser->id,
            'department_id' => $department->id,
            'team_id' => $team->id,
            'leader_user_id' => $leader->id,
        ]);
    }

    public function test_admin_cannot_create_magang_leader(): void
    {
        $admin = $this->userWithRole('admin');
        $department = Department::query()->create(['code' => 'OPS', 'name' => 'Operasional', 'is_active' => true]);
        $team = Team::query()->create(['department_id' => $department->id, 'code' => 'OPS-1', 'name' => 'Operasional Satu', 'is_active' => true]);

        $this->withSession(['_token' => 'test-token'])->withHeader('X-CSRF-TOKEN', 'test-token')->actingAs($admin)
            ->postJson(route('api.lks.admin.santri.store'), [
                'name' => 'Magang Leader',
                'email' => 'magang-leader@example.test',
                'temporary_password' => 'passwordsementara123',
                'gender' => 'ikhwan',
                'team_id' => $team->id,
                'category' => 'magang',
                'level' => 'leader',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('category');

        $this->assertDatabaseMissing('users', ['email' => 'magang-leader@example.test']);
    }

    public function test_admin_cannot_change_magang_to_leader(): void
    {
        $admin = $this->userWithRole('admin');
        $santri = $this->userWithRole('santri', ['name' => 'Santri Magang']);
        $department = Department::query()->create(['code' => 'OPS', 'name' => 'Operasional', 'is_active' => true]);
        $team = Team::query()->create(['department_id' => $department->id, 'code' => 'OPS-1', 'name' => 'Operasional Satu', 'is_active' => true]);
        $profile = SantriProfile::query()->create(['user_id' => $santri->id, 'gender' => 'ikhwan', 'department_id' => $department->id, 'team_id' => $team->id, 'category' => 'magang', 'level' => 'staff', 'status' => 'active']);

        $this->withSession(['_token' => 'test-token'])->withHeader('X-CSRF-TOKEN', 'test-token')->actingAs($admin)
            ->patchJson(route('api.lks.admin.santri.update', $profile), [
                'name' => $santri->name,
                'email' => $santri->email,
                'gender' => 'ikhwan',
                'team_id' => $team->id,
                'category' => 'magang',
                'level' => 'leader',
                'status' => 'active',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('category');
    }

    public function test_admin_cannot_assign_magang_as_team_leader(): void
    {
        $admin = $this->userWithRole('admin');
        $magang = $this->userWithRole('leader', ['name' => 'Magang Lama']);
        $department = Department::query()->create(['code' => 'OPS', 'name' => 'Operasional', 'is_active' => true]);
        $team = Team::query()->create(['department_id' => $department->id, 'code' => 'OPS-1', 'name' => 'Operasional Satu', 'is_active' => true]);
        SantriProfile::query()->create(['user_id' => $magang->id, 'gender' => 'ikhwan', 'department_id' => $department->id, 'team_id' => $team->id, 'category' => 'magang', 'level' => 'leader', 'status' => 'active']);

        $this->withSession(['_token' => 'test-token'])->withHeader('X-CSRF-TOKEN', 'test-token')->actingAs($admin)
            ->patchJson(route('api.lks.admin.teams.update', $team), ['leader_user_id' => $magang->id])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('leader_user_id');
    }

    public function test_activation_requires_both_document_official_names(): void
    {
        $admin = $this->userWithRole('admin');
        $period = $this->period($admin);
        $this->periodActivity($period, 1);

        $this->withSession(['_token' => 'test-token'])->withHeader('X-CSRF-TOKEN', 'test-token')->actingAs($admin)
            ->postJson(route('api.lks.periods.activate', $period))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('signatories');
    }

    public function test_changing_a_team_leader_updates_active_snapshots_but_not_closed_history(): void
    {
        $admin = $this->userWithRole('admin');
        $previousLeader = $this->userWithRole('leader');
        $newLeader = $this->userWithRole('leader');
        $member = $this->userWithRole('santri');
        $department = Department::query()->create(['code' => 'OPS', 'name' => 'Operations', 'is_active' => true]);
        $team = Team::query()->create(['department_id' => $department->id, 'code' => 'OPS-1', 'name' => 'Operations One', 'leader_user_id' => $previousLeader->id, 'is_active' => true]);
        foreach ([$previousLeader, $newLeader, $member] as $user) {
            SantriProfile::query()->create(['user_id' => $user->id, 'gender' => 'ikhwan', 'department_id' => $department->id, 'team_id' => $team->id, 'level' => $user->id === $member->id ? 'staff' : 'leader', 'status' => 'active']);
        }
        $active = $this->period($admin, ['status' => 'active']);
        $closed = $this->period($admin, ['status' => 'closed']);
        $activeParticipant = $this->participant($active, $member, $previousLeader);
        $activeParticipant->update(['team_id_snapshot' => $team->id]);
        $closedParticipant = $this->participant($closed, $member, $previousLeader);
        $closedParticipant->update(['team_id_snapshot' => $team->id]);

        $this->withSession(['_token' => 'test-token'])->withHeader('X-CSRF-TOKEN', 'test-token')->actingAs($admin)
            ->patchJson(route('api.lks.admin.teams.update', $team), ['leader_user_id' => $newLeader->id])
            ->assertOk();

        $this->assertDatabaseHas('period_participant_snapshots', ['id' => $activeParticipant->id, 'leader_user_id_snapshot' => $newLeader->id]);
        $this->assertDatabaseHas('period_participant_snapshots', ['id' => $closedParticipant->id, 'leader_user_id_snapshot' => $previousLeader->id]);
    }

    public function test_temporary_password_must_be_changed_before_lks_access(): void
    {
        $santri = User::factory()->create(['password' => 'sementara123', 'must_change_password' => true]);

        $this->actingAs($santri)
            ->getJson(route('api.lks.recap'))
            ->assertForbidden();

        $this->withoutMiddleware();
        $this->actingAs($santri)
            ->putJson(route('api.lks.account.password.update'), [
                'current_password' => 'sementara123',
                'password' => 'passwordbaru123',
                'password_confirmation' => 'passwordbaru123',
            ])
            ->assertOk()
            ->assertJsonPath('data.must_change_password', false);

        $this->assertDatabaseHas('users', ['id' => $santri->id, 'must_change_password' => false]);
    }

    public function test_admin_can_manage_a_document_official_identity_without_uploading_a_signature(): void
    {
        $admin = $this->userWithRole('admin');
        $signatory = DocumentSignatory::query()->where('role', 'general_manager')->firstOrFail();

        $this->withSession(['_token' => 'test-token'])->withHeader('X-CSRF-TOKEN', 'test-token')->actingAs($admin)
            ->post(route('api.lks.admin.document-signatories.update', $signatory), [
                'name' => 'Joko Wardiyanto',
            ])
            ->assertOk()
            ->assertJsonPath('data.name', 'Joko Wardiyanto')
            ->assertJsonPath('data.verification_configured', true)
            ->assertJsonMissing(['signature_path']);
    }

    public function test_document_signature_slots_are_recreated_after_configuration_data_is_cleared(): void
    {
        $admin = $this->userWithRole('admin');
        DocumentSignatory::query()->delete();

        $this->actingAs($admin)
            ->getJson(route('api.lks.admin.document-signatories.index'))
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.role', 'general_manager')
            ->assertJsonPath('data.1.role', 'kabid_pengembangan_spiritual');
    }

    public function test_santri_can_record_own_checklist_but_not_another_participants_checklist(): void
    {
        $actor = $this->userWithRole('santri');
        $other = $this->userWithRole('santri');
        $period = $this->period($actor, ['status' => 'active']);
        $activity = $this->periodActivity($period, 1);
        $actorParticipant = $this->participant($period, $actor);
        $otherParticipant = $this->participant($period, $other);

        $this->actingAs($actor)
            ->putJson(route('api.lks.checklists.store'), [
                'period_activity_id' => $activity->id,
                'checklist_date' => now()->toDateString(),
                'is_completed' => true,
            ])
            ->assertOk()
            ->assertJsonPath('data.is_completed', true);

        $this->assertDatabaseHas('lks_checklists', [
            'period_participant_id' => $actorParticipant->id,
            'period_activity_id' => $activity->id,
            'is_completed' => true,
        ]);

        $this->actingAs($actor)
            ->putJson(route('api.lks.checklists.store'), [
                'period_activity_id' => $activity->id,
                'participant_id' => $otherParticipant->id,
                'checklist_date' => now()->toDateString(),
                'is_completed' => true,
            ])
            ->assertForbidden();
    }

    public function test_santri_can_bulk_record_personal_checklists_and_existing_dates_are_skipped(): void
    {
        $this->withoutMiddleware();
        $admin = $this->userWithRole('admin');
        $santri = $this->userWithRole('santri');
        $period = $this->period($admin, ['status' => 'active']);
        $activity = $this->periodActivity($period, 4);
        $participant = $this->participant($period, $santri);
        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();
        $this->checklist($participant, $activity, $santri, $today);

        $this->actingAs($santri)
            ->postJson(route('api.lks.checklists.bulk'), [
                'period_activity_id' => $activity->id,
                'checklist_dates' => [$today, $yesterday],
            ])
            ->assertOk()
            ->assertJsonPath('data.recorded', 1)
            ->assertJsonPath('data.skipped', 1);

        $this->assertDatabaseHas('lks_checklists', [
            'period_participant_id' => $participant->id,
            'period_activity_id' => $activity->id,
            'checklist_date' => $yesterday,
            'is_completed' => true,
        ]);
    }

    public function test_santri_cannot_record_single_or_bulk_checklists_before_participation_starts(): void
    {
        $santri = $this->userWithRole('santri');
        $period = $this->period($santri, ['status' => 'active']);
        $activity = $this->periodActivity($period, 1);
        $participant = $this->participant($period, $santri);
        $participationStart = now()->startOfDay();
        $beforeParticipation = $participationStart->copy()->subDay()->toDateString();
        $participant->update(['participation_start_date' => $participationStart->toDateString()]);

        $this->actingAs($santri)
            ->putJson(route('api.lks.checklists.store'), [
                'period_activity_id' => $activity->id,
                'checklist_date' => $beforeParticipation,
                'is_completed' => true,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('checklist_date');

        $this->actingAs($santri)
            ->postJson(route('api.lks.checklists.bulk'), [
                'period_activity_id' => $activity->id,
                'checklist_dates' => [$beforeParticipation],
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('checklist_dates.'.$beforeParticipation);

        $this->assertDatabaseMissing('lks_checklists', [
            'period_participant_id' => $participant->id,
            'period_activity_id' => $activity->id,
        ]);
    }

    public function test_dashboard_clamps_a_requested_date_to_the_participation_start_date(): void
    {
        $santri = $this->userWithRole('santri');
        $period = $this->period($santri, ['status' => 'active']);
        $this->periodActivity($period, 1);
        $participant = $this->participant($period, $santri);
        $participationStart = now()->startOfDay();
        $participant->update(['participation_start_date' => $participationStart->toDateString()]);

        $this->actingAs($santri)
            ->getJson(route('api.lks.dashboard', ['date' => $participationStart->copy()->subDay()->toDateString()]))
            ->assertOk()
            ->assertJsonPath('data.selected_date', $participationStart->toDateString());
    }

    public function test_admin_cannot_add_a_participant_when_no_eligible_activity_day_remains(): void
    {
        $admin = $this->userWithRole('admin');
        $santri = $this->userWithRole('santri');
        $today = now()->startOfDay();
        $period = $this->period($admin, [
            'status' => 'active',
            'start_date' => $today->copy()->subDay()->toDateString(),
            'end_date' => $today->toDateString(),
            'target_proration_enabled' => true,
        ]);
        $activity = $this->periodActivity($period, 1);
        $activity->update(['allowed_weekdays' => '{'.$today->copy()->addDay()->isoWeekday().'}']);
        $profile = SantriProfile::query()->create([
            'user_id' => $santri->id,
            'gender' => 'ikhwan',
            'level' => 'staff',
            'status' => 'active',
        ]);

        $this->actingAs($admin)
            ->postJson(route('api.lks.periods.participants.store', [$period, $profile]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('period');

        $this->assertDatabaseMissing('period_participant_snapshots', [
            'period_id' => $period->id,
            'user_id' => $santri->id,
        ]);
    }

    public function test_dashboard_exposes_recorded_dates_for_the_personal_bulk_calendar(): void
    {
        $this->withoutMiddleware();
        $admin = $this->userWithRole('admin');
        $santri = $this->userWithRole('santri');
        $period = $this->period($admin, ['status' => 'active']);
        $activity = $this->periodActivity($period, 4);
        $participant = $this->participant($period, $santri);
        $recordedDate = now()->subDay()->toDateString();
        $this->checklist($participant, $activity, $santri, $recordedDate);

        $this->actingAs($santri)
            ->getJson(route('api.lks.dashboard', ['date' => now()->toDateString()]))
            ->assertOk()
            ->assertJsonPath('data.activities.0.recorded_dates.0', $recordedDate);
    }

    public function test_dashboard_only_returns_activities_scheduled_for_the_selected_date(): void
    {
        $admin = $this->userWithRole('admin');
        $santri = $this->userWithRole('santri');
        $period = $this->period($admin, ['status' => 'active']);
        $scheduled = $this->periodActivity($period, 1);
        $notScheduled = $this->periodActivity($period, 1);
        $scheduled->update(['allowed_weekdays' => '{'.now()->isoWeekday().'}']);
        $notScheduled->update(['allowed_weekdays' => '{'.now()->addDay()->isoWeekday().'}']);
        $this->participant($period, $santri);

        $this->actingAs($santri)
            ->getJson(route('api.lks.dashboard', ['date' => now()->toDateString()]))
            ->assertOk()
            ->assertJsonCount(1, 'data.activities')
            ->assertJsonPath('data.activities.0.id', $scheduled->id);
    }

    public function test_holiday_checklist_does_not_consume_the_weekly_limit(): void
    {
        $this->withoutMiddleware();
        $admin = $this->userWithRole('admin');
        $santri = $this->userWithRole('santri');
        $period = $this->period($admin, ['status' => 'active', 'start_date' => now()->subWeeks(2)->toDateString()]);
        $activity = $this->periodActivity($period, 4);
        $activity->update(['max_per_week' => 1]);
        $participant = $this->participant($period, $santri);
        $holidayDate = now()->copy()->startOfWeek();
        $ordinaryDate = $holidayDate->copy()->addDay();
        if ($ordinaryDate->gt(now())) {
            $holidayDate = $holidayDate->subWeek();
            $ordinaryDate = $holidayDate->copy()->addDay();
        }
        PeriodHolidaySnapshot::query()->create([
            'period_id' => $period->id,
            'holiday_date' => $holidayDate->toDateString(),
            'name' => 'Hari libur uji',
            'type' => 'public_holiday',
        ]);

        $this->actingAs($santri)
            ->putJson(route('api.lks.checklists.store'), [
                'period_activity_id' => $activity->id,
                'participant_id' => $participant->id,
                'checklist_date' => $holidayDate->toDateString(),
                'is_completed' => true,
            ])
            ->assertOk();

        $this->actingAs($santri)
            ->putJson(route('api.lks.checklists.store'), [
                'period_activity_id' => $activity->id,
                'participant_id' => $participant->id,
                'checklist_date' => $ordinaryDate->toDateString(),
                'is_completed' => true,
            ])
            ->assertOk();
    }

    public function test_bulk_checklist_identifies_dates_that_exceed_the_weekly_limit(): void
    {
        $this->withoutMiddleware();
        $admin = $this->userWithRole('admin');
        $santri = $this->userWithRole('santri');
        $period = $this->period($admin, ['status' => 'active', 'start_date' => now()->subWeeks(2)->toDateString()]);
        $activity = $this->periodActivity($period, 4);
        $activity->update(['max_per_week' => 1]);
        $participant = $this->participant($period, $santri);
        $firstDate = now()->copy()->startOfWeek();
        $secondDate = $firstDate->copy()->addDay();
        if ($secondDate->gt(now())) {
            $firstDate = $firstDate->subWeek();
            $secondDate = $firstDate->copy()->addDay();
        }

        $this->actingAs($santri)
            ->postJson(route('api.lks.checklists.bulk'), [
                'period_activity_id' => $activity->id,
                'checklist_dates' => [$firstDate->toDateString(), $secondDate->toDateString()],
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('checklist_dates.'.$secondDate->toDateString());

        $this->assertDatabaseMissing('lks_checklists', ['period_participant_id' => $participant->id]);
    }

    public function test_admin_cannot_use_personal_bulk_checklist(): void
    {
        $this->withoutMiddleware();
        $admin = $this->userWithRole('admin');

        $this->actingAs($admin)
            ->postJson(route('api.lks.checklists.bulk'), [
                'period_activity_id' => fake()->uuid(),
                'checklist_dates' => [now()->toDateString()],
            ])
            ->assertForbidden();
    }

    public function test_admin_correction_requires_a_reason_and_creates_an_audit_log(): void
    {
        $admin = $this->userWithRole('admin');
        $santri = $this->userWithRole('santri');
        $period = $this->period($admin, ['status' => 'active']);
        $activity = $this->periodActivity($period, 1);
        $participant = $this->participant($period, $santri);
        LksChecklist::query()->create([
            'period_participant_id' => $participant->id,
            'period_activity_id' => $activity->id,
            'checklist_date' => now()->toDateString(),
            'is_completed' => true,
            'recorded_by_user_id' => $santri->id,
        ]);

        $payload = [
            'period_activity_id' => $activity->id,
            'participant_id' => $participant->id,
            'checklist_date' => now()->toDateString(),
            'is_completed' => false,
        ];

        $this->actingAs($admin)
            ->putJson(route('api.lks.checklists.store'), $payload)
            ->assertUnprocessable()
            ->assertJsonValidationErrors('reason');

        $this->actingAs($admin)
            ->putJson(route('api.lks.checklists.store'), $payload + ['reason' => 'Koreksi catatan harian'])
            ->assertOk()
            ->assertJsonPath('data.is_completed', false);

        $this->assertDatabaseHas('audit_logs', [
            'actor_user_id' => $admin->id,
            'event' => 'checklist.corrected',
            'reason' => 'Koreksi catatan harian',
        ]);
    }

    public function test_admin_can_review_and_correct_multiple_checklists_for_a_participant(): void
    {
        $admin = $this->userWithRole('admin');
        $santri = $this->userWithRole('santri');
        $period = $this->period($admin, ['status' => 'active']);
        $firstActivity = $this->periodActivity($period, 1);
        $secondActivity = $this->periodActivity($period, 1);
        $participant = $this->participant($period, $santri);
        $this->checklist($participant, $firstActivity, $santri, now()->toDateString());

        $this->actingAs($admin)
            ->getJson(route('api.lks.admin.participants.checklists.context', [$period, $participant, 'date' => now()->toDateString()]))
            ->assertOk()
            ->assertJsonPath('data.participant.id', $participant->id)
            ->assertJsonPath('data.activities.0.is_completed', true);

        $this->actingAs($admin)
            ->putJson(route('api.lks.admin.participants.checklists.correct', [$period, $participant]), [
                'checklist_date' => now()->toDateString(),
                'reason' => 'Meluruskan catatan peserta',
                'activities' => [
                    ['period_activity_id' => $firstActivity->id, 'is_completed' => false],
                    ['period_activity_id' => $secondActivity->id, 'is_completed' => true],
                ],
            ])
            ->assertOk()
            ->assertJsonPath('data.updated', 2);

        $this->assertDatabaseHas('lks_checklists', [
            'period_participant_id' => $participant->id,
            'period_activity_id' => $firstActivity->id,
            'is_completed' => false,
        ]);
        $this->assertDatabaseHas('lks_checklists', [
            'period_participant_id' => $participant->id,
            'period_activity_id' => $secondActivity->id,
            'is_completed' => true,
        ]);
        $this->assertSame(2, LksChecklist::query()->where('period_participant_id', $participant->id)->count());
    }

    public function test_score_uses_equal_weights_and_caps_each_activity_at_one_hundred_percent(): void
    {
        $admin = $this->userWithRole('admin');
        $santri = $this->userWithRole('santri');
        $period = $this->period($admin, ['status' => 'active', 'final_passing_threshold' => 70]);
        $firstActivity = $this->periodActivity($period, 2);
        $secondActivity = $this->periodActivity($period, 4);
        $participant = $this->participant($period, $santri);

        foreach ([-3, -2, -1] as $days) {
            $this->checklist($participant, $firstActivity, $santri, now()->addDays($days)->toDateString());
        }
        foreach ([-3, -2] as $days) {
            $this->checklist($participant, $secondActivity, $santri, now()->addDays($days)->toDateString());
        }

        $score = app(LksScoreCalculator::class)->calculate($participant);

        $this->assertSame(75.0, $score['final_percentage']);
        $this->assertSame('tuntas', $score['final_status']);
        $this->assertSame('C', $score['grade']);
        $this->assertSame(100.0, $score['activities'][0]['percentage']);
        $this->assertSame(50.0, $score['activities'][1]['percentage']);
    }

    public function test_personal_recap_exposes_a_grade_only_after_recording_starts(): void
    {
        $admin = $this->userWithRole('admin');
        $santri = $this->userWithRole('santri');
        $period = $this->period($admin, ['status' => 'active']);
        $activity = $this->periodActivity($period, 1);
        $participant = $this->participant($period, $santri);

        $beforeRecording = $this->actingAs($santri)->getJson(route('api.lks.recap'));
        $beforeRecording->assertOk();
        $this->assertArrayHasKey('grade', $beforeRecording->json('data.participants.0'));
        $this->assertNull($beforeRecording->json('data.participants.0.grade'));

        $this->checklist($participant, $activity, $santri, now()->toDateString());

        $this->actingAs($santri)
            ->getJson(route('api.lks.recap'))
            ->assertOk()
            ->assertJsonPath('data.participants.0.grade', 'A');
    }

    public function test_final_status_uses_the_participants_job_level_and_lks_category_threshold(): void
    {
        $admin = $this->userWithRole('admin');
        $leader = $this->userWithRole('leader');
        $staff = $this->userWithRole('santri');
        $regularAtSeventyFive = $this->userWithRole('santri');
        $magang = $this->userWithRole('santri');
        $period = $this->period($admin, ['status' => 'active', 'final_passing_threshold' => 90, 'staff_passing_threshold' => 85, 'magang_passing_threshold' => 75]);
        $activity = $this->periodActivity($period, 20);
        $leaderParticipant = $this->participant($period, $leader, level: 'leader');
        $staffParticipant = $this->participant($period, $staff);
        $regularAtSeventyFiveParticipant = $this->participant($period, $regularAtSeventyFive);
        $magangParticipant = $this->participant($period, $magang);
        $magangParticipant->update(['category_snapshot' => 'magang']);

        foreach (range(1, 17) as $day) {
            $date = now()->subDays($day)->toDateString();
            $this->checklist($leaderParticipant, $activity, $leader, $date);
            $this->checklist($staffParticipant, $activity, $staff, $date);
            if ($day <= 15) {
                $this->checklist($regularAtSeventyFiveParticipant, $activity, $regularAtSeventyFive, $date);
                $this->checklist($magangParticipant, $activity, $magang, $date);
            }
        }

        $calculator = app(LksScoreCalculator::class);
        $this->assertSame(85.0, $calculator->calculate($leaderParticipant)['final_percentage']);
        $this->assertSame('belum_tuntas', $calculator->calculate($leaderParticipant)['final_status']);
        $this->assertSame('tuntas', $calculator->calculate($staffParticipant)['final_status']);
        $this->assertSame(85.0, $calculator->calculate($regularAtSeventyFiveParticipant)['passing_threshold']);
        $this->assertSame('belum_tuntas', $calculator->calculate($regularAtSeventyFiveParticipant)['final_status']);
        $this->assertSame(75.0, $calculator->calculate($magangParticipant)['final_percentage']);
        $this->assertSame(75.0, $calculator->calculate($magangParticipant)['passing_threshold']);
        $this->assertSame('tuntas', $calculator->calculate($magangParticipant)['final_status']);
    }

    public function test_closed_magang_period_keeps_its_stored_historical_threshold(): void
    {
        $admin = $this->userWithRole('admin');
        $magang = $this->userWithRole('santri');
        $period = $this->period($admin, ['status' => 'closed', 'staff_passing_threshold' => 85, 'magang_passing_threshold' => 85]);
        $activity = $this->periodActivity($period, 20);
        $participant = $this->participant($period, $magang);
        $participant->update(['category_snapshot' => 'magang']);

        foreach (range(1, 15) as $day) {
            $this->checklist($participant, $activity, $magang, now()->subDays($day)->toDateString());
        }

        $score = app(LksScoreCalculator::class)->calculate($participant);

        $this->assertSame(75.0, $score['final_percentage']);
        $this->assertSame(85.0, $score['passing_threshold']);
        $this->assertSame('belum_tuntas', $score['final_status']);
    }

    public function test_recap_scope_matches_the_viewers_role(): void
    {
        $admin = $this->userWithRole('admin');
        $leader = $this->userWithRole('leader');
        $member = $this->userWithRole('santri', ['name' => 'Anggota Leader']);
        $other = $this->userWithRole('santri', ['name' => 'Anggota Lain']);
        $period = $this->period($admin, ['status' => 'active']);
        $this->periodActivity($period, 1);
        $this->participant($period, $leader, $leader, 'leader');
        $memberParticipant = $this->participant($period, $member, $leader);
        $memberParticipant->update(['category_snapshot' => 'magang']);
        $this->participant($period, $other);

        $this->actingAs($leader)
            ->getJson(route('api.lks.recap'))
            ->assertOk()
            ->assertJsonPath('data.summary.participant_count', 1)
            ->assertJsonPath('data.participants.0.user_id', $member->id)
            ->assertJsonPath('data.participants.0.level', 'staff')
            ->assertJsonPath('data.participants.0.category', 'magang');

        $this->actingAs($leader)
            ->getJson(route('api.lks.recap', ['scope' => 'personal']))
            ->assertOk()
            ->assertJsonPath('data.summary.participant_count', 1)
            ->assertJsonPath('data.participants.0.user_id', $leader->id);

        $this->actingAs($member)
            ->getJson(route('api.lks.recap'))
            ->assertOk()
            ->assertJsonPath('data.summary.participant_count', 1)
            ->assertJsonPath('data.participants.0.user_id', $member->id);

        $this->actingAs($admin)
            ->getJson(route('api.lks.recap'))
            ->assertOk()
            ->assertJsonPath('data.summary.participant_count', 3);
    }

    public function test_history_scope_keeps_leader_team_and_personal_periods_separate(): void
    {
        $admin = $this->userWithRole('admin');
        $leader = $this->userWithRole('leader');
        $member = $this->userWithRole('santri');
        $other = $this->userWithRole('santri');
        $teamPeriod = $this->period($admin, ['status' => 'closed', 'name' => 'Riwayat Tim']);
        $personalPeriod = $this->period($admin, ['status' => 'closed', 'name' => 'Riwayat Pribadi']);
        $otherPeriod = $this->period($admin, ['status' => 'closed', 'name' => 'Riwayat Lain']);
        $this->participant($teamPeriod, $member, $leader);
        $this->participant($personalPeriod, $leader);
        $this->participant($otherPeriod, $other);

        $this->actingAs($leader)
            ->getJson(route('api.lks.periods.history', ['scope' => 'team']))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $teamPeriod->id);

        $this->actingAs($leader)
            ->getJson(route('api.lks.periods.history', ['scope' => 'personal']))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $personalPeriod->id);

        $this->actingAs($member)
            ->getJson(route('api.lks.periods.history'))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $teamPeriod->id);
    }

    public function test_recap_uses_the_latest_closed_period_when_no_active_period_exists(): void
    {
        $admin = $this->userWithRole('admin');
        $older = $this->period($admin, ['status' => 'closed', 'end_date' => now()->subMonth()->toDateString()]);
        $latest = $this->period($admin, ['status' => 'closed', 'end_date' => now()->subDay()->toDateString()]);

        $this->actingAs($admin)
            ->getJson(route('api.lks.recap'))
            ->assertOk()
            ->assertJsonPath('data.period.id', $latest->id);

        $this->assertNotSame($older->id, $latest->id);
    }

    public function test_admin_recap_trends_include_stable_leader_and_gender_groups(): void
    {
        $admin = $this->userWithRole('admin');
        $leader = $this->userWithRole('leader');
        $member = $this->userWithRole('santri');
        $period = $this->period($admin, ['status' => 'closed']);
        $participant = $this->participant($period, $member, $leader);
        $participant->update(['gender_snapshot' => 'akhwat', 'category_snapshot' => 'magang']);

        $this->actingAs($admin)
            ->getJson(route('api.lks.department-trends'))
            ->assertOk()
            ->assertJsonPath('data.0.groups.leader.0.id', $leader->id)
            ->assertJsonPath('data.0.groups.gender.0.id', 'akhwat')
            ->assertJsonPath('data.0.groups.category.0.id', 'magang');
    }

    public function test_role_trends_keep_leader_and_santri_scopes_separate(): void
    {
        $admin = $this->userWithRole('admin');
        $leader = $this->userWithRole('leader');
        $member = $this->userWithRole('santri');
        $other = $this->userWithRole('santri');
        $teamPeriod = $this->period($admin, ['status' => 'closed', 'name' => 'Tren Tim']);
        $personalPeriod = $this->period($admin, ['status' => 'closed', 'name' => 'Tren Pribadi Leader']);
        $otherPeriod = $this->period($admin, ['status' => 'closed', 'name' => 'Tren Lain']);
        $this->participant($teamPeriod, $member, $leader);
        $this->participant($personalPeriod, $leader);
        $this->participant($otherPeriod, $other);

        $this->actingAs($leader)
            ->getJson(route('api.lks.role-trends'))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.period.id', $teamPeriod->id)
            ->assertJsonPath('data.0.participant_count', 1);

        $this->actingAs($member)
            ->getJson(route('api.lks.role-trends'))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.period.id', $teamPeriod->id)
            ->assertJsonPath('data.0.participant_count', 1);

        $this->actingAs($admin)
            ->getJson(route('api.lks.role-trends'))
            ->assertForbidden();
    }

    public function test_pdf_exports_follow_the_viewers_access_scope(): void
    {
        $admin = $this->userWithRole('admin');
        $santri = $this->userWithRole('santri');
        $otherSantri = $this->userWithRole('santri');
        $period = $this->period($admin, ['status' => 'active']);
        $this->periodActivity($period, 1);
        $participant = $this->participant($period, $santri);

        $this->actingAs($santri)
            ->get(route('api.lks.exports.personal', $period))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->actingAs($otherSantri)
            ->get(route('api.lks.exports.personal', $period))
            ->assertNotFound();

        $this->actingAs($admin)
            ->get(route('api.lks.admin.exports.periods.summary', $period))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->actingAs($admin)
            ->get(route('api.lks.admin.exports.participants.show', [$period, $participant]))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_individual_pdf_with_ten_activities_fits_a_single_a4_page(): void
    {
        $admin = $this->userWithRole('admin');
        $santri = $this->userWithRole('santri');
        $period = $this->period($admin, ['status' => 'closed', 'name' => 'Periode Pengujian Lembar Kendali Spiritual Oktober Dua Ribu Dua Puluh Enam']);

        // shortcut: covers the current 10-item LKS catalog; add a page strategy if periods can exceed it.
        foreach (range(1, 10) as $target) {
            $this->periodActivity($period, $target);
        }

        $this->participant($period, $santri)->update(['category_snapshot' => 'magang']);

        $pdf = $this->actingAs($santri)
            ->get(route('api.lks.exports.personal', $period))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->assertSame(1, preg_match_all('/\/Type\s*\/Page\b/', $pdf->getContent()));
        $verification = LksDocumentVerification::query()->sole();

        $this->get(route('lks.verification.show', $verification))
            ->assertOk()
            ->assertSee('Dokumen ini Resmi &amp; Sah', false)
            ->assertSee($verification->document_code)
            ->assertDontSee($santri->name)
            ->assertDontSee($period->name);
    }

    public function test_santri_can_manage_their_own_profile_and_password(): void
    {
        $santri = $this->userWithRole('santri');

        $this->actingAs($santri)
            ->putJson(route('api.lks.account.profile.update'), [
                'name' => 'Nama Baru',
                'email' => 'nama.baru@example.test',
            ])
            ->assertOk()
            ->assertJsonPath('data.name', 'Nama Baru');

        $this->actingAs($santri)
            ->putJson(route('api.lks.account.password.update'), [
                'current_password' => 'password',
                'password' => 'password-baru',
                'password_confirmation' => 'password-baru',
            ])
            ->assertOk()
            ->assertJsonPath('data.updated', true);

        $this->assertTrue(Hash::check('password-baru', $santri->fresh()->password));
    }

    public function test_admin_can_import_calendar_holidays(): void
    {
        $admin = $this->userWithRole('admin');
        $this->withoutMiddleware();

        $this->actingAs($admin)
            ->postJson(route('api.lks.admin.calendar-holidays.import'), [
                'holidays' => [
                    ['holiday_date' => '2026-01-01', 'name' => 'Tahun Baru', 'type' => 'public_holiday'],
                    ['holiday_date' => '2026-01-16', 'name' => 'Cuti Bersama', 'type' => 'collective_leave'],
                ],
            ])
            ->assertOk()
            ->assertJsonPath('data.count', 2);

        $this->assertDatabaseHas('calendar_holidays', ['holiday_date' => '2026-01-01', 'type' => 'public_holiday']);
        $this->assertDatabaseHas('calendar_holidays', ['holiday_date' => '2026-01-16', 'type' => 'collective_leave']);
    }

    public function test_new_draft_period_automatically_snapshots_work_calendar_holidays(): void
    {
        $admin = $this->userWithRole('admin');
        $this->withoutMiddleware();

        $this->actingAs($admin)
            ->postJson(route('api.lks.admin.calendar-holidays.store'), [
                'holiday_date' => '2026-10-12',
                'name' => 'Hari Libur Uji',
                'type' => 'public_holiday',
            ])
            ->assertCreated();

        $response = $this->actingAs($admin)
            ->postJson(route('api.lks.admin.periods.store'), [
                'name' => 'Oktober 2026',
                'start_date' => '2026-10-01',
                'end_date' => '2026-10-31',
                'final_passing_threshold' => 90,
                'staff_passing_threshold' => 85,
            ])
            ->assertCreated()
            ->assertJsonPath('data.magang_passing_threshold', '75.00')
            ->assertJsonPath('data.holiday_snapshots.0.name', 'Hari Libur Uji');

        $this->assertDatabaseHas('period_holiday_snapshots', [
            'period_id' => $response->json('data.id'),
            'holiday_date' => '2026-10-12',
            'name' => 'Hari Libur Uji',
        ]);
    }

    public function test_new_draft_period_automatically_snapshots_active_master_activities(): void
    {
        $admin = $this->userWithRole('admin');
        $this->withoutMiddleware();
        LksActivity::query()->update(['is_active' => false]);
        $activity = LksActivity::query()->create([
            'code' => 'AUTO-SNAPSHOT', 'name' => 'Aktivitas otomatis', 'is_active' => true, 'sort_order' => 1,
            'default_target_count' => 20, 'default_minimum_target_count' => 14,
            'default_applicable_genders' => '{ikhwan,akhwat}', 'default_applicable_levels' => '{leader,staff}',
        ]);

        $response = $this->actingAs($admin)
            ->postJson(route('api.lks.admin.periods.store'), [
                'name' => 'Oktober 2026',
                'start_date' => '2026-10-01',
                'end_date' => '2026-10-31',
                'final_passing_threshold' => 90,
                'staff_passing_threshold' => 85,
            ])
            ->assertCreated();

        $this->assertDatabaseHas('period_activities', [
            'period_id' => $response->json('data.id'),
            'activity_id' => $activity->id,
            'target_count' => 20,
            'minimum_target_count' => 14,
            'magang_target_count' => 15,
            'magang_minimum_target_count' => 11,
        ]);
    }

    public function test_admin_can_sync_missing_master_activities_to_a_draft_without_overwriting_existing_rules(): void
    {
        $admin = $this->userWithRole('admin');
        $this->withoutMiddleware();
        LksActivity::query()->update(['is_active' => false]);
        $period = $this->period($admin);
        $existingMaster = LksActivity::query()->create([
            'code' => 'EXISTING', 'name' => 'Aktivitas tersedia', 'is_active' => true, 'sort_order' => 1,
            'default_target_count' => 20, 'default_minimum_target_count' => 14,
            'default_applicable_genders' => '{ikhwan,akhwat}', 'default_applicable_levels' => '{leader,staff}',
        ]);
        $missingMaster = LksActivity::query()->create([
            'code' => 'MISSING', 'name' => 'Aktivitas belum disalin', 'is_active' => true, 'sort_order' => 2,
            'default_target_count' => 8, 'default_minimum_target_count' => 6,
            'default_applicable_genders' => '{ikhwan}', 'default_applicable_levels' => '{leader}',
        ]);
        $existingPeriodActivity = PeriodActivity::query()->create([
            'period_id' => $period->id, 'activity_id' => $existingMaster->id,
            'activity_code_snapshot' => $existingMaster->code, 'activity_name_snapshot' => $existingMaster->name,
            'target_count' => 17, 'minimum_target_count' => 12, 'weight' => 1, 'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->postJson(route('api.lks.admin.periods.activities.sync', $period))
            ->assertOk()
            ->assertJsonPath('data.added_count', 1);

        $this->assertSame(17, $existingPeriodActivity->fresh()->target_count);
        $this->assertDatabaseHas('period_activities', [
            'period_id' => $period->id,
            'activity_id' => $missingMaster->id,
            'target_count' => 8,
            'minimum_target_count' => 6,
        ]);

        $this->actingAs($admin)
            ->postJson(route('api.lks.admin.periods.activities.sync', $period))
            ->assertOk()
            ->assertJsonPath('data.added_count', 0);
    }

    public function test_admin_can_delete_activity_used_only_by_draft_period(): void
    {
        $admin = $this->userWithRole('admin');
        $periodActivity = $this->periodActivity($this->period($admin), 4);
        $this->withoutMiddleware();
        $activity = LksActivity::query()->findOrFail($periodActivity->activity_id);

        $this->actingAs($admin)
            ->deleteJson(route('api.lks.admin.activities.destroy', $activity))
            ->assertNoContent();

        $this->assertDatabaseMissing('lks_activities', ['id' => $periodActivity->activity_id]);
        $this->assertDatabaseMissing('period_activities', ['id' => $periodActivity->id]);
    }

    public function test_admin_cannot_delete_activity_used_by_active_period(): void
    {
        $admin = $this->userWithRole('admin');
        $periodActivity = $this->periodActivity($this->period($admin, ['status' => 'active']), 4);
        $this->withoutMiddleware();
        $activity = LksActivity::query()->findOrFail($periodActivity->activity_id);

        $this->actingAs($admin)
            ->deleteJson(route('api.lks.admin.activities.destroy', $activity))
            ->assertUnprocessable();

        $this->assertDatabaseHas('lks_activities', ['id' => $periodActivity->activity_id]);
        $this->assertDatabaseHas('period_activities', ['id' => $periodActivity->id]);
    }

    private function userWithRole(string $role, array $attributes = []): User
    {
        $user = User::factory()->create($attributes);
        $roleModel = Role::query()->firstOrCreate(['code' => $role], ['name' => ucfirst($role)]);
        $user->roles()->attach($roleModel);

        return $user;
    }

    private function period(User $creator, array $attributes = []): LksPeriod
    {
        $defaults = [
            'name' => 'Periode Uji',
            'start_date' => now()->subWeek()->toDateString(),
            'end_date' => now()->addWeek()->toDateString(),
            'status' => 'draft',
            'final_passing_threshold' => 90,
            'created_by' => $creator->id,
        ];
        $defaults['recording_enabled'] = ($attributes['status'] ?? $defaults['status']) === 'active';

        return LksPeriod::query()->create(array_merge($defaults, $attributes));
    }

    private function periodActivity(LksPeriod $period, int $target): PeriodActivity
    {
        $sequence = LksActivity::query()->count() + 1;
        $activity = LksActivity::query()->create([
            'code' => "ACT-{$sequence}",
            'name' => "Aktivitas {$sequence}",
            'is_active' => true,
            'sort_order' => $sequence,
        ]);

        return PeriodActivity::query()->create([
            'period_id' => $period->id,
            'activity_id' => $activity->id,
            'activity_code_snapshot' => $activity->code,
            'activity_name_snapshot' => $activity->name,
            'target_count' => $target,
            'minimum_target_count' => $target,
            'weight' => 1,
            'is_active' => true,
            'sort_order' => $sequence,
        ]);
    }

    private function participant(LksPeriod $period, User $user, ?User $leader = null, string $level = 'staff'): PeriodParticipantSnapshot
    {
        return PeriodParticipantSnapshot::query()->create([
            'period_id' => $period->id,
            'user_id' => $user->id,
            'participant_name_snapshot' => $user->name,
            'gender_snapshot' => 'ikhwan',
            'leader_user_id_snapshot' => $leader?->id,
            'leader_name_snapshot' => $leader?->name,
            'level_snapshot' => $level,
            'participation_start_date' => $period->start_date->toDateString(),
        ]);
    }

    private function checklist(PeriodParticipantSnapshot $participant, PeriodActivity $activity, User $actor, string $date): void
    {
        LksChecklist::query()->create([
            'period_participant_id' => $participant->id,
            'period_activity_id' => $activity->id,
            'checklist_date' => $date,
            'is_completed' => true,
            'recorded_by_user_id' => $actor->id,
        ]);
    }

    private function configureActivationSignatories(): void
    {
        foreach (['general_manager', 'kabid_pengembangan_spiritual'] as $role) {
            DocumentSignatory::query()->where('role', $role)->update([
                'name' => $role === 'general_manager' ? 'General Manager' : 'Kabid Spiritual',
            ]);
        }
    }
}
