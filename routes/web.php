<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OtDutyController;
use App\Http\Controllers\SubscriptionPaymentController;
use App\Http\Controllers\StaffDirectoryController;
use App\Http\Controllers\Superadmin\SubscriptionSettingsController;
use App\Http\Middleware\EnsureSubscriptionIsActive;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('ot-duty.index');
});

Route::get('/subscription/payment', [SubscriptionPaymentController::class, 'show'])->name('subscription.payment');
Route::post('/subscription/payment', [SubscriptionPaymentController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('subscription.payment.submit');

Route::get('/ot-duty', [OtDutyController::class, 'index'])
    ->middleware(EnsureSubscriptionIsActive::class)
    ->name('ot-duty.index');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
});

Route::middleware(['auth', 'can:manage-subscription-settings'])
    ->prefix('superadmin')
    ->name('superadmin.')
    ->group(function () {
        Route::get('/subscription-settings', [SubscriptionSettingsController::class, 'index'])->name('subscription-settings');
        Route::put('/subscription-settings/trial', [SubscriptionSettingsController::class, 'updateTrial'])->name('subscription-settings.trial.update');
        Route::put('/subscription-settings', [SubscriptionSettingsController::class, 'update'])->name('subscription-settings.update');
        Route::patch('/subscription-payments/{payment}/approve', [SubscriptionSettingsController::class, 'approve'])->name('subscription-payments.approve');
        Route::patch('/subscription-payments/{payment}/reject', [SubscriptionSettingsController::class, 'reject'])->name('subscription-payments.reject');
        Route::get('/subscription-payments/{payment}/proof', [SubscriptionSettingsController::class, 'proof'])->name('subscription-payments.proof');
    });

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware(EnsureSubscriptionIsActive::class)->group(function () {
        Route::get('/dashboard', DashboardController::class)->name('dashboard');

        Route::middleware('can:manage-staff-directory')->prefix('staff-directory')->name('staff-directory.')->group(function () {
            Route::get('/', [StaffDirectoryController::class, 'index'])->name('index');
            Route::get('/create', [StaffDirectoryController::class, 'create'])->name('create');
            Route::post('/', [StaffDirectoryController::class, 'store'])->name('store');
            Route::get('/{staffMember}/edit', [StaffDirectoryController::class, 'edit'])->name('edit');
            Route::put('/{staffMember}', [StaffDirectoryController::class, 'update'])->name('update');
            Route::delete('/{staffMember}', [StaffDirectoryController::class, 'destroy'])->name('destroy');
        });

        Route::get('/ot-duty/create', [OtDutyController::class, 'create'])->name('ot-duty.create')->middleware('can:manage-ot-duty');
        Route::post('/ot-duty', [OtDutyController::class, 'store'])->name('ot-duty.store')->middleware('can:manage-ot-duty');
        Route::get('/ot-duty/{otDuty}', [OtDutyController::class, 'show'])->name('ot-duty.show')->middleware('can:access-ot-duty');
        Route::get('/ot-duty/{otDuty}/edit', [OtDutyController::class, 'edit'])->name('ot-duty.edit')->middleware('can:manage-ot-duty');
        Route::put('/ot-duty/{otDuty}', [OtDutyController::class, 'update'])->name('ot-duty.update')->middleware('can:manage-ot-duty');
        Route::delete('/ot-duty/{otDuty}', [OtDutyController::class, 'destroy'])->name('ot-duty.destroy')->middleware('can:manage-ot-duty');
    });
});
