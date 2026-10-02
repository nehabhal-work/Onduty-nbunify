<?php

namespace App\Providers;

use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->ensureDefaultUsers();

        Gate::define('access-ot-duty', function (?User $user) {
            return $user !== null;
        });

        Gate::define('manage-ot-duty', function (?User $user) {
            return $user?->isAdmin() === true || $user?->isSuperAdmin() === true;
        });

        Gate::define('manage-staff-directory', function (?User $user) {
            return $user?->isAdmin() === true || $user?->isSuperAdmin() === true;
        });

        Gate::define('manage-subscription-settings', function (?User $user) {
            return $user?->isSuperAdmin() === true;
        });

        View::composer('layouts.app', function ($view) {
            $subscriptions = app(SubscriptionService::class);
            $settings = $subscriptions->settings();
            $trialEndsAt = $subscriptions->trialEndsAt($settings);

            $view->with([
                'subscriptionSettings' => $settings,
                'subscriptionTrialEndsAt' => $trialEndsAt,
                'subscriptionTrialActive' => now()->lessThan($trialEndsAt),
            ]);
        });
    }

    protected function ensureDefaultUsers(): void
    {
        // if (! User::where('email', 'admin@example.com')->exists()) {
        //     User::create([
        //         'name' => 'Admin User',
        //         'email' => 'admin@example.com',
        //         'password' => Hash::make('password'),
        //         'role' => 'admin',
        //     ]);
        // }

        // if (! User::where('email', 'manager@example.com')->exists()) {
        //     User::create([
        //         'name' => 'Manager User',
        //         'email' => 'manager@example.com',
        //         'password' => Hash::make('password'),
        //         'role' => 'manager',
        //     ]);
        // }
    }
}
