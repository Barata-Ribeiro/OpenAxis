<?php

use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Controllers\Settings\SessionController;
use App\Http\Controllers\Settings\TwoFactorAuthenticationController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('auth')->prefix('settings')->group(function () {
    Route::redirect('settings', '/settings/profile');

    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('addresses', [ProfileController::class, 'indexAddresses'])->name('profile.addresses');
    Route::post('addresses', [ProfileController::class, 'storeAddress'])->name('profile.addresses.store');
    Route::patch('addresses/{address}', [ProfileController::class, 'updateAddress'])->name('profile.addresses.update');
    Route::delete('addresses/{address}', [ProfileController::class, 'destroyAddress'])->name('profile.addresses.destroy');

    Route::get('password', [PasswordController::class, 'edit'])->name('user-password.edit');

    Route::put('password', [PasswordController::class, 'update'])
        ->middleware('throttle:6,1')
        ->name('user-password.update');

    Route::get('appearance', function () {
        return Inertia::render('settings/appearance');
    })->name('appearance.edit');

    Route::get('two-factor', [TwoFactorAuthenticationController::class, 'show'])
        ->name('two-factor.show');

    Route::get('sessions', [SessionController::class, 'index'])->name('sessions.index');
    Route::delete('sessions/{session_id}', [SessionController::class, 'destroy'])->name('sessions.destroy');
});
