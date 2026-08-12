<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function changePassword(){
        return view('profiles.change-password');
    }
    public function updatePassword( ChangePasswordRequest $request ): RedirectResponse
    {
        auth()->user()->update([
            'password' => Hash::make($request->password),
        ]);
        return redirect()
            ->back()
            ->with(
                'success',
                __('common.messages.password_changed')
            );
    }
}
