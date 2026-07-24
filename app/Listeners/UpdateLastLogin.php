<?php

namespace App\Listeners;

use App\Models\LoginHistory;
use Illuminate\Auth\Events\Login;
use Jenssegers\Agent\Agent;

class UpdateLastLogin
{
    public function handle(Login $event): void
    {
        $agent = new Agent();
        $browser = $agent->browser();
        $platform = $agent->platform();
        $device = $agent->device();
        $user = $event->user;
        $user->updateQuietly([
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
            'last_login_browser' => $browser,
            'last_login_platform' => $platform,
            'login_count' => $user->login_count + 1,
        ]);

        LoginHistory::create([
            'user_id' => $user->id,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'browser' => $browser,
            'platform' => $platform,
            'device' => $device,
            'is_mobile' => $agent->isMobile(),
            'logged_in_at' => now(),
        ]);
    }
}