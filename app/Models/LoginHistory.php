<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginHistory extends Model
{
    protected $fillable = [
        'user_id',
        'ip',
        'user_agent',
        'browser',
        'platform',
        'device',
        'is_mobile',
        'logged_in_at',
    ];
}
