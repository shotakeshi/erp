<?php

namespace App\Models;

use config\database\factories\UserFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Enums\UserStatus;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'uuid',
        'status',
        'last_login_at',
        'last_login_ip',
        'activated_at',
        'created_by',
        'updated_by',
        'last_login_browser',
        'last_login_platform',
        'login_count',
        'activation_token',
        'activation_expires_at',
        'email_verified_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'full_name',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => UserStatus::class,
            'last_login_at' => 'datetime'
        ];
    }

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function loginHistories()
    {
        return $this->hasMany(LoginHistory::class);
    }

    // User.php
    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    protected static function booted(): void
    {
        static::creating(callback: function (User $user) {
            if (empty($user->password)) {
                $user->password = Hash::make(
                    Str::password(16)
                );
            }
            if (auth()->check() && empty($user->created_by)) {
                $user->created_by = auth()->id();
            }
        });

        static::updating(function (User $user) {

        });
    }
}
