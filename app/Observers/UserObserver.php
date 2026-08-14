<?php

namespace App\Observers;

use App\Models\User;
use App\Notifications\AccountActivationNotification;
use Illuminate\Support\Str;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        $token = Str::random(64);

        $user->updateQuietly([
            'activation_token' => hash('sha256', $token),
            'activation_expires_at' => now()->addHours(24),
        ]);

        $user->notify(
            new AccountActivationNotification($token)
        );
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
