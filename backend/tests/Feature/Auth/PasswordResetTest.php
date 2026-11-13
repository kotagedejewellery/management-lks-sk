<?php

namespace Tests\Feature\Auth;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;
use Laravel\Fortify\Features;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(PreventRequestForgery::class);

        $this->skipUnlessFortifyHas(Features::resetPasswords());
    }

    public function test_reset_password_link_screen_can_be_rendered(): void
    {
        $response = $this->get(route('password.request'));

        $response->assertOk();
    }

    public function test_reset_password_link_can_be_requested(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post(route('password.request'), ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class);

        $audit = AuditLog::query()->where('event', 'password_reset.link_requested')->sole();
        $this->assertNull($audit->actor_user_id);
        $this->assertSame($user->getMorphClass(), $audit->auditable_type);
        $this->assertSame($user->getKey(), $audit->auditable_id);
        $this->assertSame(['delivery' => 'accepted_by_mailer'], $audit->after_data);
    }

    public function test_unknown_or_inactive_accounts_receive_the_same_reset_request_response(): void
    {
        Notification::fake();

        $inactiveUser = User::factory()->create(['is_active' => false]);

        $this->post(route('password.request'), ['email' => 'unknown@example.test'])
            ->assertSessionHas('status', 'passwords.sent');

        $this->post(route('password.request'), ['email' => $inactiveUser->email])
            ->assertSessionHas('status', 'passwords.sent');

        Notification::assertNothingSent();
        $this->assertDatabaseMissing('audit_logs', ['event' => 'password_reset.link_requested']);
    }

    public function test_password_reset_request_explains_the_next_step_without_revealing_account_status(): void
    {
        $this->withSession(['status' => 'passwords.sent'])
            ->get(route('password.request'))
            ->assertOk()
            ->assertSee('Jika alamat email ini terdaftar pada akun LKS yang aktif, tautan reset akan dikirim.')
            ->assertSee('Periksa Inbox dan Spam. Jika belum diterima dalam beberapa menit, pastikan email benar atau hubungi Admin LKS.');
    }

    public function test_password_reset_throttle_explains_how_long_to_wait(): void
    {
        $this->withSession(['status' => 'passwords.throttled'])
            ->get(route('password.request'))
            ->assertOk()
            ->assertSee('Tunggu sekitar satu menit sebelum meminta tautan reset password baru.');
    }

    public function test_password_reset_requests_are_limited_per_ip_address(): void
    {
        $server = ['REMOTE_ADDR' => '198.51.100.24'];

        foreach (range(1, 5) as $attempt) {
            $this->withServerVariables($server)
                ->post(route('password.request'), ['email' => "unknown-{$attempt}@example.test"])
                ->assertSessionHas('status', 'passwords.sent');
        }

        $this->withServerVariables($server)
            ->post(route('password.request'), ['email' => 'unknown-six@example.test'])
            ->assertSessionHas('status', 'passwords.throttled');
    }

    public function test_reset_password_screen_can_be_rendered(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post(route('password.request'), ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) {
            $response = $this->get(route('password.reset', $notification->token));

            $response->assertOk();

            return true;
        });
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post(route('password.request'), ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
            $response = $this->post(route('password.update'), [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

            $response
                ->assertSessionHasNoErrors()
                ->assertRedirect(route('login', absolute: false));

            return true;
        });
    }

    public function test_password_reset_clears_temporary_password_requirement_and_existing_sessions(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'must_change_password' => true,
            'remember_token' => 'old-remember-token',
        ]);
        DB::table('sessions')->insert([
            'id' => 'other-device-session',
            'user_id' => $user->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit',
            'payload' => 'test',
            'last_activity' => now()->timestamp,
        ]);

        $this->post(route('password.request'), ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
            $this->post(route('password.update'), [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'password-baru',
                'password_confirmation' => 'password-baru',
            ])->assertRedirect(route('login', absolute: false));

            return true;
        });

        $user->refresh();

        $this->assertFalse($user->must_change_password);
        $this->assertNotSame('old-remember-token', $user->remember_token);
        $this->assertDatabaseMissing('sessions', ['id' => 'other-device-session']);
    }
}
