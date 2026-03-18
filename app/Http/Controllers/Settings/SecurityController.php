<?php

namespace App\Http\Controllers\Settings;

use App\Concerns\PasswordValidationRules;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Fortify\Features;

class SecurityController extends Controller
{
    use PasswordValidationRules;

    public function edit(Request $request): Response|RedirectResponse
    {
        $canManageTwoFactor = Features::enabled(Features::twoFactorAuthentication());

        if (
            $canManageTwoFactor
            && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword')
            && ! $request->session()->has('auth.password_confirmed_at')
        ) {
            return to_route('password.confirm');
        }

        $props = [
            'canManageTwoFactor' => $canManageTwoFactor,
        ];

        if ($canManageTwoFactor) {
            $props['requiresConfirmation'] = Features::optionEnabled(Features::twoFactorAuthentication(), 'confirm');
            $props['twoFactorEnabled'] = ! is_null($request->user()?->two_factor_secret);
        }

        return Inertia::render('settings/Security', $props);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => $this->currentPasswordRules(),
            'password' => $this->passwordRules(),
        ]);

        $request->user()->forceFill([
            'password' => $validated['password'],
        ])->save();

        return to_route('security.edit');
    }
}
