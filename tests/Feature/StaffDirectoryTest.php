<?php

namespace Tests\Feature;

use App\Models\OtDuty;
use App\Models\StaffMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffDirectoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_admin_and_superadmin_can_open_staff_directory(): void
    {
        $this->get(route('staff-directory.index'))->assertRedirect(route('login'));

        $manager = User::factory()->create(['role' => 'manager']);
        $this->actingAs($manager)
            ->get(route('staff-directory.index'))
            ->assertForbidden();

        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)
            ->get(route('staff-directory.index'))
            ->assertOk()
            ->assertSee('Sisters &amp; Technicians', false);

        $superadmin = User::factory()->create(['role' => 'superadmin']);
        $this->actingAs($superadmin)->get(route('staff-directory.index'))->assertOk();
    }

    public function test_staff_can_be_created_with_only_name_and_optional_fields_are_validated(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->post(route('staff-directory.store'), [
                'type' => 'sister',
                'name' => 'New Sister',
            ])
            ->assertRedirect(route('staff-directory.index'));

        $this->assertDatabaseHas('staff_members', [
            'type' => 'sister',
            'name' => 'New Sister',
            'mobile' => null,
            'email' => null,
            'address' => null,
        ]);

        $this->actingAs($admin)
            ->from(route('staff-directory.create', ['type' => 'technician']))
            ->post(route('staff-directory.store'), [
                'type' => 'technician',
                'name' => 'Invalid Contact',
                'mobile' => 'not-a-number',
                'email' => 'not-an-email',
                'address' => str_repeat('a', 501),
            ])
            ->assertSessionHasErrors(['mobile', 'email', 'address']);
    }

    public function test_new_staff_names_are_accepted_by_assignment_dropdowns_and_save(): void
    {
        StaffMember::create(['type' => 'sister', 'name' => 'Test Sister']);
        StaffMember::create(['type' => 'technician', 'name' => 'Test Technician']);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get('/ot-duty/create')
            ->assertOk()
            ->assertSee('Test Sister')
            ->assertSee('Test Technician');

        $this->actingAs($admin)
            ->post('/ot-duty', [
                'section' => '2nd floor',
                'sister_name' => 'Test Sister',
                'technician_name' => 'Test Technician',
                'date_time' => '2026-10-02T10:00',
                'ot_no' => 1,
                'shift' => 'Morning',
                'department' => 'ENT',
                'unit_no' => 1,
            ])
            ->assertRedirect(route('ot-duty.index'));

        $this->assertDatabaseHas('ot_duties', [
            'sister_name' => 'Test Sister',
            'technician_name' => 'Test Technician',
        ]);
    }

    public function test_renaming_updates_assignments_and_assigned_staff_cannot_be_deleted(): void
    {
        $member = StaffMember::query()->where('type', 'sister')->where('name', 'Mery')->firstOrFail();
        OtDuty::create([
            'section' => '2nd floor',
            'sister_name' => 'Mery',
            'technician_name' => 'Mery',
            'date_time' => now(),
            'ot_no' => 1,
            'shift' => 'Morning',
            'department' => 'ENT',
            'unit_no' => 1,
        ]);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->put(route('staff-directory.update', $member), ['name' => 'Mery Updated'])
            ->assertRedirect(route('staff-directory.index'));

        $this->assertDatabaseHas('ot_duties', ['sister_name' => 'Mery Updated', 'technician_name' => 'Mery']);

        $this->actingAs($admin)
            ->delete(route('staff-directory.destroy', $member->fresh()))
            ->assertSessionHasErrors('staff_member');

        $this->assertDatabaseHas('staff_members', ['id' => $member->id, 'name' => 'Mery Updated']);
    }
}
