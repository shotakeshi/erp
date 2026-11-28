<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class LeaveTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'code' => Str::upper((string) $this->input('code')),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('leave_types', 'code')->ignore($this->route('leave_type')),
            ],
            'description' => ['nullable', 'string', 'max:5000'],
            'is_paid' => ['required', 'boolean'],
            'allow_half_day' => ['required', 'boolean'],
            'deduct_from_balance' => ['required', 'boolean'],
            'is_carry_forward' => ['required', 'boolean'],
            'max_carry_forward' => ['required', 'decimal:0,2', 'min:0', 'max:3.00'],
            'max_days_per_year' => ['nullable', 'decimal:0,2', 'min:0', 'max:99.00'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => __('site.leave_types.name'),
            'code' => __('site.leave_types.code'),
            'description' => __('site.leave_types.description'),
            'is_paid' => __('site.leave_types.is_paid'),
            'allow_half_day' => __('site.leave_types.allow_half_day'),
            'deduct_from_balance' => __('site.leave_types.deduct_from_balance'),
            'is_carry_forward' => __('site.leave_types.is_carry_forward'),
            'max_carry_forward' => __('site.leave_types.max_carry_forward'),
            'max_days_per_year' => __('site.leave_types.max_days_per_year'),
            'is_active' => __('site.leave_types.is_active'),
        ];
    }
}
