<?php

namespace App\Services;

use App\Models\SubscriptionSetting;
use Illuminate\Support\Carbon;

class SubscriptionService
{
    public function settings(): SubscriptionSetting
    {
        return SubscriptionSetting::current();
    }

    public function trialEndsAt(?SubscriptionSetting $settings = null): Carbon
    {
        $settings ??= $this->settings();

        $trialStartedAt = $settings->trial_started_at ?? now();

        if ($settings->trial_duration_minutes) {
            return $trialStartedAt->copy()->addMinutes($settings->trial_duration_minutes);
        }

        return $trialStartedAt->copy()->addDays($settings->trial_duration_days);
    }

    public function accessEndsAt(?SubscriptionSetting $settings = null): Carbon
    {
        $settings ??= $this->settings();
        $trialEndsAt = $this->trialEndsAt($settings);

        if ($settings->paid_until && $settings->paid_until->greaterThan($trialEndsAt)) {
            return $settings->paid_until;
        }

        return $trialEndsAt;
    }

    public function hasAccess(?SubscriptionSetting $settings = null): bool
    {
        $settings ??= $this->settings();
        $now = now();

        return $now->lessThan($this->trialEndsAt($settings))
            || ($settings->paid_until && $now->lessThan($settings->paid_until));
    }

    public function extendOneMonth(SubscriptionSetting $settings): void
    {
        $renewalStart = $settings->paid_until && $settings->paid_until->isFuture()
            ? $settings->paid_until->copy()
            : now();

        $settings->paid_until = $renewalStart->addMonthNoOverflow();
        $settings->save();
    }
}