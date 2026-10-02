<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPayment;
use App\Services\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SubscriptionPaymentController extends Controller
{
    public function show(SubscriptionService $subscriptions): View
    {
        $settings = $subscriptions->settings();

        return view('subscription.payment', [
            'settings' => $settings,
            'trialEndsAt' => $subscriptions->trialEndsAt($settings),
            'accessEndsAt' => $subscriptions->accessEndsAt($settings),
            'hasAccess' => $subscriptions->hasAccess($settings),
        ]);
    }

    public function store(Request $request, SubscriptionService $subscriptions): RedirectResponse
    {
        $data = $request->validate([
            'payer_name' => ['required', 'string', 'max:150'],
            'payer_email' => ['required', 'email', 'max:255'],
            'reference' => ['required', 'string', 'max:120'],
            'payment_screenshot' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $subscriptions->settings();
        $data['proof_path'] = $request->file('payment_screenshot')->store('subscription/payment-proofs', 'local');

        SubscriptionPayment::create([
            ...$data,
            'user_id' => $request->user()?->id,
        ]);

        return redirect()
            ->route('subscription.payment')
            ->with('success', 'Payment details submitted. Access will resume after the superadmin confirms your payment.');
    }
}