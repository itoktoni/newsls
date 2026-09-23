<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('settings/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('settings/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('settings/profile/sessions/delete', [ProfileController::class, 'destroyOtherSessions'])->name('profile.sessions.delete');
    Route::delete('settings/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('settings/env', [SettingsController::class, 'index'])->name('settings.env');
    Route::post('settings/env', [SettingsController::class, 'save'])->name('settings.env.save');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('settings/appearance', 'pages::settings.appearance')->name('appearance.edit');

    Route::livewire('settings/security', 'pages::settings.security')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('security.edit');
});

Route::middleware(['auth', 'verified', 'access'])->group(function () {
    Route::auto('/activity-log', 'ActivityLogController', ['name' => 'activity-log']);
});
