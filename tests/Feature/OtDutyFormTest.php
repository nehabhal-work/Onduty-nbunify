<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\OtDutyOptions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OtDutyFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_view_has_datetime_field_and_alphabetical_option_lists(): void
    {
        $expectedSisters = [
            'Aarya',
            'Abdul',
            'Amrapali',
            'Ashfaq',
            'Aurang',
            'Avinash',
            'Dilip',
            'Dipali N',
            'Jai',
            'Kailash',
            'Kirti',
            'Madhukar',
            'Mayuresh',
            'Mery',
            'Mrunal',
            'Nirmala',
            'Noor',
            'Pallavi',
            'Prasad',
            'Purva',
            'Rahel',
            'Renuka',
            'Roshani',
            'Ruchita',
            'Rupali',
            'Sagar',
            'Sahil',
            'Sai',
            'Sachin',
            'Shraddha',
            'Shubham',
            'Sonali T',
            'Sudha',
            'Surekha',
            'Surjeet',
            'Tushar',
            'Usha',
            '-',
        ];
        sort($expectedSisters, SORT_STRING | SORT_FLAG_CASE);

        $expectedTechnicians = $expectedSisters;

        $this->assertSame($expectedSisters, OtDutyOptions::sisters());
        $this->assertSame($expectedTechnicians, OtDutyOptions::technicians());
        $this->assertSame([
            '2nd floor',
            '4th floor',
            'LR OT',
            'Recovery',
            'Scope OT',
            'Night On Call',
        ], OtDutyOptions::sections());
        $this->assertSame([0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 17, 18, 19, 20, 21], OtDutyOptions::otNumbers());
        $this->assertSame(['Morning', 'Evening', 'Night', 'Double Duty'], OtDutyOptions::shifts());
        $this->assertSame([0, 1, 2, 3, 4, 5, 6], OtDutyOptions::units());
        $this->assertContains('Ophthalmic', OtDutyOptions::departments());
        $this->assertContains('Pseudodental', OtDutyOptions::departments());

        $admin = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->get('/ot-duty/create');

        $response->assertOk();
        $response->assertSee('name="date_time"', false);
        $response->assertSee('name="section"', false);
        $response->assertSee('data-duty-field="ot_no"', false);
        $response->assertSee('data-duty-field="department"', false);
        $response->assertSee('data-duty-field="unit_no"', false);
        $response->assertSee('data-duty-field="technician_name"', false);
        $response->assertSee('data-duty-field="surgery"', false);
        $response->assertSee('form-select', false);

        foreach (OtDutyOptions::sections() as $section) {
            $response->assertSee($section);
        }
    }

    public function test_recovery_assignment_saves_hidden_fields_as_null(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->post('/ot-duty', array_merge($this->assignment('Recovery'), [
                'technician_name' => '',
                'surgery' => '',
            ]))
            ->assertRedirect(route('ot-duty.index'));

        $this->assertDatabaseHas('ot_duties', [
            'section' => 'Recovery',
            'technician_name' => null,
            'ot_no' => null,
            'department' => null,
            'unit_no' => null,
            'surgery' => null,
        ]);
    }

    public function test_lr_assignment_uses_obgy_and_has_no_ot_number(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->post('/ot-duty', $this->assignment('LR OT'))
            ->assertRedirect(route('ot-duty.index'));

        $this->assertDatabaseHas('ot_duties', [
            'section' => 'LR OT',
            'ot_no' => null,
            'department' => 'OBGY',
            'unit_no' => 1,
        ]);
    }

    public function test_second_floor_rejects_ot_numbers_17_through_21(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->from('/ot-duty/create')
            ->post('/ot-duty', array_merge($this->assignment('2nd floor'), ['ot_no' => 17]))
            ->assertSessionHasErrors('ot_no');
    }

    public function test_night_on_call_does_not_require_ot_number_or_unit(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->post('/ot-duty', array_merge($this->assignment('Night On Call'), [
                'ot_no' => '',
                'unit_no' => '',
            ]))
            ->assertRedirect(route('ot-duty.index'));

        $this->assertDatabaseHas('ot_duties', [
            'section' => 'Night On Call',
            'ot_no' => null,
            'unit_no' => null,
        ]);
    }

    public function test_fourth_floor_accepts_only_ot_numbers_17_through_21_and_omits_unit(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->post('/ot-duty', array_merge($this->assignment('4th floor'), ['ot_no' => 17]))
            ->assertRedirect(route('ot-duty.index'));

        $this->assertDatabaseHas('ot_duties', [
            'section' => '4th floor',
            'ot_no' => 17,
            'unit_no' => null,
        ]);

        $this->actingAs($admin)
            ->from('/ot-duty/create')
            ->post('/ot-duty', array_merge($this->assignment('4th floor'), ['ot_no' => 1]))
            ->assertSessionHasErrors('ot_no');
    }

    private function assignment(string $section): array
    {
        return [
            'section' => $section,
            'sister_name' => 'Abdul',
            'technician_name' => 'Amrapali',
            'date_time' => '2026-09-27T10:00',
            'ot_no' => 1,
            'shift' => 'Morning',
            'department' => 'ENT',
            'unit_no' => 1,
        ];
    }
}
