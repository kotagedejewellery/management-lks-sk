<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_users_can_visit_the_dashboard(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('dashboard'));
        $response->assertOk()
            ->assertSee('LKS Santri Karya')
            ->assertSee('/lks-prototype/styles.css')
            ->assertSee('app.js?v=')
            ->assertSee('overrides.css?v=')
            ->assertHeader('Cache-Control', 'private, no-store');

        $this->get('/lks-prototype/app.js')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/javascript; charset=UTF-8')
            ->assertHeader('Cache-Control', 'public, max-age=31536000, immutable')
            ->assertSee('data-open-bulk-checklist')
            ->assertSee('Catat beberapa tanggal');
    }
}
