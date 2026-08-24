<?php

namespace App\Http\Requests;

use App\Enums\UserStatus;
use App\Models\Employee;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

abstract class TeamAssignmentsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_ids' => ['required', 'array', 'min:1'],
            'employee_ids.*' => ['required', 'integer', 'distinct'],
            'start_date' => ['required', 'date', 'before_or_equal:today'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $this->ensureEmployeesAreOperational($validator);
            },
        ];
    }

    public function messages(): array
    {
        return [
            'employee_ids.*.distinct' => __('site.teams.validation.employee_ids_distinct'),
            'start_date.before_or_equal' => __('site.teams.validation.date_cannot_be_future'),
        ];
    }

    public function attributes(): array
    {
        return [
            'employee_ids' => __('site.teams.employee_ids'),
            'employee_ids.*' => __('site.teams.employee'),
            'start_date' => __('site.teams.start_date'),
        ];
    }

    private function ensureEmployeesAreOperational(Validator $validator): void
    {
        if ($validator->errors()->has('employee_ids')
            || $validator->errors()->get('employee_ids.*') !== []
        ) {
            return;
        }

        $employeeIds = array_map('intval', $this->input('employee_ids', []));

        $eligibleCount = Employee::query()
            ->whereKey($employeeIds)
            ->whereHas('user', fn ($query) => $query->where('status', UserStatus::ACTIVE))
            ->count();

        if ($eligibleCount !== count($employeeIds)) {
            $validator->errors()->add(
                'employee_ids',
                __('site.teams.validation.employees_must_be_operational'),
            );
        }
    }
}
