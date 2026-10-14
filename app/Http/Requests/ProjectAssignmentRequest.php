<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectAssignmentRequest extends FormRequest
{
    /**
     * Error bag used by the role form on each card, kept separate from the add-assignment modal
     * because both forms use the `role` field.
     */
    public const UPDATE_ERROR_BAG = 'assignmentUpdate';

    protected function prepareForValidation(): void
    {
        if ($this->isMethod('put')) {
            $this->errorBag = self::UPDATE_ERROR_BAG;
        }
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if ($this->isMethod('put')) {
            return [
                'role' => ['required', 'string', 'max:50'],
            ];
        }

        return [
            'employee_ids' => ['required', 'array', 'min:1'],
            'employee_ids.*' => ['required', 'integer', 'distinct', 'exists:employees,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'employee_ids' => __('site.projects.employee'),
            'employee_ids.*' => __('site.projects.employee'),
            'role' => __('site.projects.role'),
        ];
    }
}
