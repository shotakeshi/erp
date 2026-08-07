<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PositionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'department_id' => ['required', 'exists:departments,id'],
            'level' => ['nullable', 'string', 'max:50'],
            'salary_min' => ['nullable', 'numeric', 'min:0'],
            'salary_max' => ['nullable', 'numeric', 'gte:salary_min'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Custom attribute names.
     */
    public function attributes(): array
    {
        return [
            'name' => __('site.positions.name'),
            'department_id' => __('site.positions.department'),
            'level' => __('site.positions.level_grade'),
            'salary_min' => __('site.positions.salary_min'),
            'salary_max' => __('site.positions.salary_max'),
            'description' => __('site.positions.description'),
        ];
    }
}
