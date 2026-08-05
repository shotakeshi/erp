<?php

namespace App\Models;

use App\Enums\UserStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\FileHelpers;

class Employee extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'employee_id', 'first_name', 'last_name', 'email', 'phone',
        'dob', 'gender', 'nationality', 'address', 'city', 'state', 'country', 'postal_code',
        'department_id', 'position_id', 'reporting_manager_id', 'date_of_joining',
        'contract_type', 'salary',
    ];

    protected $casts = [
        'dob'             => 'date',
        'date_of_joining' => 'date',
        'salary'          => 'decimal:2',
    ];

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getEmployeeIdFullNameAttribute(): string
    {
        return "{$this->employee_id} | {$this->first_name} {$this->last_name}";
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function scopeActive($query)
    {
        return $query->whereHas('user', function ($query) {
            $query->where('status', UserStatus::ACTIVE);
        });
    }
}
