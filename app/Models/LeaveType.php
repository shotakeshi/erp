<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'is_paid',
        'allow_half_day',
        'deduct_from_balance',
        'is_carry_forward',
        'max_carry_forward',
        'max_days_per_year',
        'is_active',
    ];

    protected $casts = [
        'is_paid' => 'boolean',
        'is_carry_forward' => 'boolean',
        'is_active' => 'boolean',
        'allow_half_day' => 'boolean',
        'deduct_from_balance' => 'boolean',
        'max_carry_forward' => 'decimal:2',
        'max_days_per_year' => 'decimal:2',
    ];

    protected $attributes = [
        'is_active' => false,
        'is_paid' => false,
        'allow_half_day' => false,
        'deduct_from_balance' => false,
        'is_carry_forward' => false,
        'max_carry_forward' => 0,
    ];
}
