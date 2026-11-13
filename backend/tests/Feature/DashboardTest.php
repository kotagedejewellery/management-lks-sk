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
            ->assertSee('overrides.css?v=');
        $this->assertStringContainsString('private', $response->headers->get('Cache-Control', ''));
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control', ''));

        $assetResponse = $this->get('/lks-prototype/app.js');
        $assetResponse
            ->assertOk()
            ->assertHeader('Content-Type', 'application/javascript; charset=UTF-8')
            ->assertSee('data-open-bulk-checklist')
            ->assertSee('Catat beberapa tanggal');
        $this->assertStringContainsString('public', $assetResponse->headers->get('Cache-Control', ''));
        $this->assertStringContainsString('max-age=31536000', $assetResponse->headers->get('Cache-Control', ''));
        $this->assertStringContainsString('immutable', $assetResponse->headers->get('Cache-Control', ''));
    }
}
