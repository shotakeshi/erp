<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TeamAssignmentsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_ids' => ['required', 'array', 'min:1'],
            'employee_ids.*' => ['required', 'integer', 'distinct', 'exists:employees,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'employee_ids.*.distinct' => __('site.teams.validation.employee_ids_distinct'),
        ];
    }

    public function attributes(): array
    {
        return [
            'employee_ids' => __('site.teams.employee_ids'),
            'employee_ids.*' => __('site.teams.employee'),
        ];
    }
}
