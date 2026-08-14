<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function activate(string $token): View
    {
        $user = $this->findByActivationToken($token);
        abort_if(
            !$user,
            404,
            __('common.messages.activation_link_invalid')
        );

        return view('auth.activate', [
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function completeActivation( Request $request, string $token ): RedirectResponse
    {
        $request->validate([
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed'
            ],
        ]);

        $user = $this->findByActivationToken($token);

        if (!$user) {
            return back()->withErrors([
                'password' => __('common.messages.activation_link_invalid'),
            ]);
        }

        DB::transaction(function () use ($user, $request) {
            $user->update([
                'password' => Hash::make($request->password),
                'status' => \App\Enums\UserStatus::ACTIVE->value,
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

    public function forgotPassword(Request $request): View
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => [
                'required',
                'email',
            ],
        ]);

        $status = Password::sendResetLink([
            'email' => $request->email,
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

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);

        $status = Password::reset(
            $request->only([
                'email',
                'password',
                'password_confirmation',
                'token',
            ]),
            function ($user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60)
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