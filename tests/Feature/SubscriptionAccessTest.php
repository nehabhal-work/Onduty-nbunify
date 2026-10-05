<?php

namespace Tests\Feature;

use App\Models\SubscriptionPayment;
use App\Models\SubscriptionSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SubscriptionAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_expired_trial_redirects_the_ot_list_to_the_payment_page(): void
    {
        $settings = SubscriptionSetting::current();
        $settings->update(['trial_started_at' => now()->subDays(16)]);

        $this->get('/ot-duty')->assertRedirect(route('subscription.payment'));
        $this->get('/subscription/payment')
            ->assertOk()
            ->assertSee('Renew access')
            ->assertSee('Transaction reference')
            ->assertSee('name="payment_screenshot"', false)
            ->assertSee('enctype="multipart/form-data"', false);
    }

    public function test_active_trial_shows_a_live_countdown(): void
    {
        SubscriptionSetting::current();

        $this->get('/ot-duty')
            ->assertOk()
            ->assertSee('data-trial-countdown', false)
            ->assertSee('ending '.now()->addDays(15)->format('d M Y'));
    }

    public function test_payment_page_shows_hdfc_details_and_a_upi_payment_link(): void
    {
        $this->get('/subscription/payment')
            ->assertOk()
            ->assertSee('HDFC Bank (Primary)')
            ->assertSee('502000117622680')
            ->assertSee('HDFC0002504')
            ->assertSee('7710020126@hdfc')
            ->assertSee('Open UPI app');
    }

    public function test_uploaded_payment_qr_uses_the_current_request_host(): void
    {
        $settings = SubscriptionSetting::current();
        $settings->update(['qr_code_path' => 'subscription/payment-qr.jpg']);

        $this->get('/subscription/payment')
            ->assertOk()
            ->assertSee('src="'.url('/storage/subscription/payment-qr.jpg').'"', false);
    }

    public function test_testing_duration_expires_after_three_minutes(): void
    {
        $settings = SubscriptionSetting::current();
        $settings->update(['paid_until' => now()->addMonth()]);
        $superadmin = User::factory()->create(['role' => 'superadmin']);

        $this->actingAs($superadmin)
            ->put(route('superadmin.subscription-settings.trial.update'), [
                'trial_duration' => '3m',
                'trial_start_date' => today()->toDateString(),
            ])
            ->assertRedirect();

        $settings = SubscriptionSetting::current()->fresh();
        $this->assertSame(3, $settings->trial_duration_minutes);
        $this->assertNull($settings->paid_until);
        $this->assertSame(now()->format('Y-m-d H:i:s'), $settings->trial_started_at->format('Y-m-d H:i:s'));

        $manager = User::factory()->create(['role' => 'manager']);
        $this->actingAs($manager)->get('/ot-duty')->assertOk()->assertSee('data-trial-countdown', false);

        $this->travel(3)->minutes();
        $this->get('/ot-duty')->assertRedirect(route('subscription.payment'));
    }

    public function test_payment_request_approval_activates_one_month_of_access(): void
    {
        $settings = SubscriptionSetting::current();
        $settings->update(['trial_started_at' => now()->subDays(16)]);
        Storage::fake('local');

        $this->post(route('subscription.payment.submit'), [
            'payer_name' => 'Hospital Accounts',
            'payer_email' => 'accounts@example.com',
            'reference' => 'UPI-REFERENCE-001',
            'payment_screenshot' => UploadedFile::fake()->image('payment.png'),
        ])->assertRedirect(route('subscription.payment'));

        $payment = SubscriptionPayment::query()->firstOrFail();
        $this->assertNotNull($payment->proof_path);
        Storage::disk('local')->assertExists($payment->proof_path);
        $superadmin = User::factory()->create(['role' => 'superadmin']);
        $approvalTime = now();
        $this->travelTo($approvalTime);

        $this->actingAs($superadmin)
            ->get(route('superadmin.subscription-payments.proof', $payment))
            ->assertOk();

        $manager = User::factory()->create(['role' => 'manager']);
        $this->actingAs($manager)
            ->get(route('superadmin.subscription-payments.proof', $payment))
            ->assertForbidden();

        $this->actingAs($superadmin)
            ->patch(route('superadmin.subscription-payments.approve', $payment))
            ->assertRedirect();

        $this->assertDatabaseHas('subscription_payments', [
            'id' => $payment->id,
            'status' => 'approved',
        ]);
        $this->assertSame(
            $approvalTime->copy()->addMonthNoOverflow()->format('Y-m-d H:i:s'),
            $settings->fresh()->paid_until->format('Y-m-d H:i:s')
        );
        $this->get('/ot-duty')->assertOk();

        $this->actingAs($superadmin)
            ->patch(route('superadmin.subscription-payments.approve', $payment))
            ->assertStatus(409);
    }

    public function test_subscription_settings_are_superadmin_only(): void
    {
        $settings = SubscriptionSetting::current();
        $settings->update(['trial_started_at' => now()->subDays(16)]);

        $manager = User::factory()->create(['role' => 'manager']);
        $this->actingAs($manager)
            ->get(route('superadmin.subscription-settings'))
            ->assertForbidden();

        $superadmin = User::factory()->create(['role' => 'superadmin']);
        $this->actingAs($superadmin)
            ->get(route('superadmin.subscription-settings'))
            ->assertOk()
            ->assertSee('name="trial_start_date"', false)
            ->assertSee('name="trial_duration"', false)
            ->assertSee('3 minutes (testing only)')
            ->assertSee('Trial and payment details')
            ->assertSee('Payment requests');
    }

    public function test_superadmin_can_set_trial_length_and_payment_details(): void
    {
        $superadmin = User::factory()->create(['role' => 'superadmin']);

        $this->actingAs($superadmin)
            ->put(route('superadmin.subscription-settings.trial.update'), [
                'trial_duration' => '30d',
                'trial_start_date' => today()->toDateString(),
            ])
            ->assertRedirect();

        $this->actingAs($superadmin)
            ->put(route('superadmin.subscription-settings.update'), [
                'upi_id' => '7710020126@hdfc',
                'bank_details' => 'UPI: hospital@example',
                'payment_instructions' => 'Include the hospital name in the payment note.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('subscription_settings', [
            'id' => 1,
            'trial_duration_days' => 30,
            'bank_details' => 'UPI: hospital@example',
        ]);
        $this->assertSame(today()->toDateString(), SubscriptionSetting::current()->fresh()->trial_started_at->toDateString());
    }
}
