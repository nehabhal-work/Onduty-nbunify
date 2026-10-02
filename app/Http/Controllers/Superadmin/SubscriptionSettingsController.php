<?php

namespace App\Http\Controllers\Superadmin;

use App\Models\SubscriptionPayment;
use App\Models\SubscriptionSetting;
use App\Services\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as RoutingController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SubscriptionSettingsController extends RoutingController
{
    public function index(SubscriptionService $subscriptions): View
    {
        $settings = $subscriptions->settings();

        return view('superadmin.subscription-settings', [
            'settings' => $settings,
            'trialEndsAt' => $subscriptions->trialEndsAt($settings),
            'accessEndsAt' => $subscriptions->accessEndsAt($settings),
            'hasAccess' => $subscriptions->hasAccess($settings),
            'payments' => SubscriptionPayment::query()->latest()->paginate(15),
        ]);
    }

    public function updateTrial(Request $request, SubscriptionService $subscriptions): RedirectResponse
    {
        $allowedDurations = app()->environment(['local', 'testing'])
            ? ['15d', '30d', '3m']
            : ['15d', '30d'];

        $data = $request->validate([
            'trial_duration' => ['required', Rule::in($allowedDurations)],
            'trial_start_date' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'],
        ]);

        $settings = $subscriptions->settings();
        $isTestTrial = $data['trial_duration'] === '3m';
        $settings->update([
            'trial_duration_days' => $isTestTrial ? $settings->trial_duration_days : (int) rtrim($data['trial_duration'], 'd'),
            'trial_duration_minutes' => $isTestTrial ? 3 : null,
            'paid_until' => $isTestTrial ? null : $settings->paid_until,
            'trial_started_at' => $isTestTrial
                ? now()
                : \Illuminate\Support\Carbon::createFromFormat('Y-m-d', $data['trial_start_date'])->startOfDay(),
        ]);

        return back()->with('success', 'Trial schedule updated.');
    }

    public function update(Request $request, SubscriptionService $subscriptions): RedirectResponse
    {
        $data = $request->validate([
            'upi_id' => ['nullable', 'string', 'max:255'],
            'bank_details' => ['nullable', 'string', 'max:4000'],
            'payment_instructions' => ['nullable', 'string', 'max:4000'],
            'qr_code' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $settings = $subscriptions->settings();
        $oldQrCode = $settings->qr_code_path;
        $settings->fill([
            'upi_id' => $data['upi_id'] ?? null,
            'bank_details' => $data['bank_details'] ?? null,
            'payment_instructions' => $data['payment_instructions'] ?? null,
        ]);

        if ($request->hasFile('qr_code')) {
            $settings->qr_code_path = $request->file('qr_code')->store('subscription', 'public');
        }

        $settings->save();

        if ($request->hasFile('qr_code') && $oldQrCode) {
            Storage::disk('public')->delete($oldQrCode);
        }

        return back()->with('success', 'Subscription settings saved.');
    }

    public function approve(Request $request, SubscriptionPayment $payment, SubscriptionService $subscriptions): RedirectResponse
    {
        DB::transaction(function () use ($request, $payment, $subscriptions) {
            $lockedPayment = SubscriptionPayment::query()->lockForUpdate()->findOrFail($payment->id);
            abort_unless($lockedPayment->status === 'pending', 409);

            $subscriptions->settings();
            $settings = SubscriptionSetting::query()->lockForUpdate()->findOrFail(1);
            $subscriptions->extendOneMonth($settings);

            $lockedPayment->update([
                'status' => 'approved',
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
            ]);
        });

        return back()->with('success', 'Payment approved. One month of access has been added.');
    }

    public function reject(Request $request, SubscriptionPayment $payment): RedirectResponse
    {
        abort_unless($payment->status === 'pending', 409);

        $payment->update([
            'status' => 'rejected',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Payment request rejected.');
    }

    public function proof(SubscriptionPayment $payment)
    {
        abort_unless($payment->proof_path, 404);

        $disk = Storage::disk('local');
        abort_unless($disk->exists($payment->proof_path), 404);

        return response()->file($disk->path($payment->proof_path), [
            'Content-Type' => $disk->mimeType($payment->proof_path),
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}