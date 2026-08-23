<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AccountRequest;
use App\Http\Requests\Auth\PasswordResetLinkRequest;
use App\Http\Requests\Auth\PasswordResetRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function activate(string $token): View
    {
        $user = $this->findByActivationToken($token);
        abort_if(
            ! $user,
            404,
            __('common.messages.activation_link_invalid')
        );

        return view('auth.activate', [
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function completeActivation(AccountRequest $request, string $token): RedirectResponse
    {
        $validated = $request->validated();

        $user = $this->findByActivationToken($token);

        if (! $user) {
            return back()->withErrors([
                'password' => __('common.messages.activation_link_invalid'),
            ]);
        }

        DB::transaction(function () use ($user, $validated) {
            $user->update([
                'password' => Hash::make($validated['password']),
                'status' => UserStatus::ACTIVE,
                'email_verified_at' => now(),
                'activated_at' => now(),
                'activation_token' => null,
                'activation_expires_at' => null,
            ]);
        });

        return redirect()
            ->route('login')
            ->with(
                'success',
                __('common.messages.account_activated')
            );
    }

    private function findByActivationToken(string $token): ?User
    {
        return User::query()
            ->where(
                'activation_token',
                hash('sha256', $token)
            )
            ->where(
                'activation_expires_at',
                '>',
                now()
            )
            ->whereNull('activated_at')
            ->first();
    }

    public function forgotPassword(): View
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(PasswordResetLinkRequest $request): RedirectResponse
    {
        $status = Password::sendResetLink([
            'email' => $request->validated('email'),
        ]);

        return $status === Password::RESET_LINK_SENT
            ? back()->with(
                'success',
                __('common.messages.password_reset_link_sent')
            )
            : back()->withErrors([
                'email' => __($status),
            ]);
    }

    public function resetPassword(string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => request('email'),
        ]);
    }

    public function updatePassword(PasswordResetRequest $request): RedirectResponse
    {
        $status = Password::reset(
            $request->validated(),
            function ($user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()
                ->route('login')
                ->with(
                    'success',
                    __('common.messages.password_reset_success')
                )
            : back()->withErrors([
                'email' => __($status),
            ]);
    }
}
