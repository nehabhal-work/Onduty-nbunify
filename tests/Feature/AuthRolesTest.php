<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthRolesTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_only_access_the_ot_duty_list(): void
    {
        $this->get('/')->assertRedirect(route('ot-duty.index'));

        $this->get('/ot-duty')
            ->assertOk()
            ->assertSee('OT Duty List')
            ->assertSee('ot-duty-filter-form', false)
            ->assertDontSee('Add Assignment')
            ->assertDontSee('title="View"', false)
            ->assertDontSee('title="Edit"', false)
            ->assertDontSee('title="Delete"', false);

        $this->get('/ot-duty/create')->assertRedirect(route('login'));
    }

    public function test_login_page_is_accessible_and_manager_cannot_create_assignment(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);

        $manager = User::factory()->create([
            'email' => 'manager@example.com',
            'role' => 'manager',
        ]);

        $this->get('/login')
            ->assertOk()
            ->assertSee('data-password-toggle', false)
            ->assertSee('aria-label="Show password"', false)
            ->assertSee('href="'.asset('css/app.css').'?v=', false)
            ->assertSee('src="'.asset('js/app.js').'?v=', false)
            ->assertSee('href="'.asset('vendor/bootstrap/bootstrap.min.css').'?v=', false)
            ->assertSee('src="'.asset('vendor/qrcode/qrcode.min.js').'?v=', false)
            ->assertDontSee('@vite', false);

        $this->actingAs($admin)->get('/ot-duty/create')->assertOk();

        $this->actingAs($manager)->get('/ot-duty/create')->assertForbidden();
        $this->actingAs($manager)->get('/ot-duty')->assertOk();
    }

    public function test_dashboard_requires_login_and_is_available_to_staff(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));

        $manager = User::factory()->create(['role' => 'manager']);

        $this->actingAs($manager)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Assignments today')
            ->assertSee('OT Duty Assignment');
    }
}
