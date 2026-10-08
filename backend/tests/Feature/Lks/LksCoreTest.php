<?php

namespace Tests\Feature\Lks;

use App\Models\Department;
use App\Models\DocumentSignatory;
use App\Models\LksActivity;
use App\Models\LksChecklist;
use App\Models\LksPeriod;
use App\Models\PeriodActivity;
use App\Models\PeriodParticipantSnapshot;
use App\Models\Role;
use App\Models\SantriProfile;
use App\Models\Team;
use App\Models\User;
use App\Services\LksScoreCalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
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

    public function test_admin_can_activate_a_draft_period_and_snapshot_active_santri(): void
    {
        $admin = $this->userWithRole('admin');
        $santri = $this->userWithRole('santri');
        $department = Department::query()->create(['code' => 'ENG', 'name' => 'Engineering', 'is_active' => true]);
        $team = Team::query()->create(['department_id' => $department->id, 'code' => 'WEB', 'name' => 'Web', 'is_active' => true]);
        SantriProfile::query()->create([
            'user_id' => $santri->id,
            'gender' => 'ikhwan',
            'department_id' => $department->id,
            'team_id' => $team->id,
            'status' => 'active',
        ]);
        $period = $this->period($admin);
        $this->periodActivity($period, 1);

        $this->actingAs($admin)
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

    public function test_admin_can_manage_a_private_digital_document_signature(): void
    {
        Storage::fake('local');
        $admin = $this->userWithRole('admin');
        $signatory = DocumentSignatory::query()->where('role', 'general_manager')->firstOrFail();

        $this->actingAs($admin)
            ->post(route('api.lks.admin.document-signatories.update', $signatory), [
                'name' => 'Joko Wardiyanto',
                'signature' => UploadedFile::fake()->image('general-manager.png', 400, 160),
            ])
            ->assertOk()
            ->assertJsonPath('data.name', 'Joko Wardiyanto')
            ->assertJsonPath('data.signature_configured', true);

        Storage::disk('local')->assertExists($signatory->fresh()->signature_path);
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
        $this->assertSame(100.0, $score['activities'][0]['percentage']);
        $this->assertSame(50.0, $score['activities'][1]['percentage']);
    }

    public function test_final_status_uses_the_participants_job_level_threshold(): void
    {
        $admin = $this->userWithRole('admin');
        $leader = $this->userWithRole('leader');
        $staff = $this->userWithRole('santri');
        $period = $this->period($admin, ['status' => 'active', 'final_passing_threshold' => 90, 'staff_passing_threshold' => 85]);
        $activity = $this->periodActivity($period, 20);
        $leaderParticipant = $this->participant($period, $leader, level: 'leader');
        $staffParticipant = $this->participant($period, $staff);

        foreach (range(1, 17) as $day) {
            $date = now()->subDays($day)->toDateString();
            $this->checklist($leaderParticipant, $activity, $leader, $date);
            $this->checklist($staffParticipant, $activity, $staff, $date);
        }

        $calculator = app(LksScoreCalculator::class);
        $this->assertSame(85.0, $calculator->calculate($leaderParticipant)['final_percentage']);
        $this->assertSame('belum_tuntas', $calculator->calculate($leaderParticipant)['final_status']);
        $this->assertSame('tuntas', $calculator->calculate($staffParticipant)['final_status']);
    }

    public function test_recap_scope_matches_the_viewers_role(): void
    {
        $admin = $this->userWithRole('admin');
        $leader = $this->userWithRole('leader');
        $member = $this->userWithRole('santri', ['name' => 'Anggota Leader']);
        $other = $this->userWithRole('santri', ['name' => 'Anggota Lain']);
        $period = $this->period($admin, ['status' => 'active']);
        $this->periodActivity($period, 1);
        $this->participant($period, $member, $leader);
        $this->participant($period, $other);

        $this->actingAs($leader)
            ->getJson(route('api.lks.recap'))
            ->assertOk()
            ->assertJsonPath('data.summary.participant_count', 1)
            ->assertJsonPath('data.participants.0.user_id', $member->id);

        $this->actingAs($admin)
            ->getJson(route('api.lks.recap'))
            ->assertOk()
            ->assertJsonPath('data.summary.participant_count', 2);
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

    private function userWithRole(string $role, array $attributes = []): User
    {
        $user = User::factory()->create($attributes);
        $roleModel = Role::query()->firstOrCreate(['code' => $role], ['name' => ucfirst($role)]);
        $user->roles()->attach($roleModel);

        return $user;
    }

    private function period(User $creator, array $attributes = []): LksPeriod
    {
        return LksPeriod::query()->create(array_merge([
            'name' => 'Periode Uji',
            'start_date' => now()->subWeek()->toDateString(),
            'end_date' => now()->addWeek()->toDateString(),
            'status' => 'draft',
            'final_passing_threshold' => 90,
            'created_by' => $creator->id,
        ], $attributes));
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
}
