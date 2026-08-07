<?php

namespace App\Http\Requests;

use App\Enums\ContractType;
use App\Enums\Gender;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class EmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->normalizeData();
    }

    public function rules(): array
    {
        return array_merge(
            $this->personalRules(),
            $this->addressRules(),
            $this->employmentRules()
        );
    }

    /**
     * --------------------------------------------------------------------------
     * Personal Information
     * --------------------------------------------------------------------------
     */
    private function personalRules(): array
    {
        $employeeId = $this->employeeId();

        return [
            'avatar' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'employee_id' => [
                'required',
                'string',
                'max:20',
                Rule::unique('employees')->ignore($employeeId),
            ],

            'first_name' => [
                'required',
                'string',
                'max:100',
            ],

            'last_name' => [
                'required',
                'string',
                'max:100',
            ],

            'email' => [
                'required',
                'email:rfc,dns',
                'max:255',
                Rule::unique('employees')->ignore($employeeId),
            ],

            'phone' => [
                'required',
                'string',
                'max:20',
                'regex:/^0\d{9}$/',
            ],

            'dob' => [
                'nullable',
                'date_format:d/m/Y',
            ],

            'gender' => [
                'required',
                new Enum(Gender::class),
            ],
        ];
    }

    /**
     * --------------------------------------------------------------------------
     * Address
     * --------------------------------------------------------------------------
     */
    private function addressRules(): array
    {
        return [
            'address' => [
                'nullable',
                'string',
                'max:255',
            ],

            'portal_code' => [
                'nullable',
                'string',
                'max:20',
            ],

            'city' => [
                'nullable',
                'string',
            ],

            'state' => [
                'nullable',
                'string',
            ],
        ];
    }

    /**
     * --------------------------------------------------------------------------
     * Employment
     * --------------------------------------------------------------------------
     */
    private function employmentRules(): array
    {
        return [
            'department_id' => [
                'nullable',
                'exists:departments,id',
            ],

            'position_id' => [
                'nullable',
                'exists:positions,id',
            ],

            'reporting_manager_id' => [
                'nullable',
                'exists:employees,id',
            ],

            'date_of_joining' => [
                'required',
                'date_format:d/m/Y',
            ],

            'contract_type' => [
                'required',
                new Enum(ContractType::class),
            ],

            'salary' => [
                'nullable',
                'numeric',
                'min:0',
            ],
        ];
    }

    /**
     * --------------------------------------------------------------------------
     * Helpers
     * --------------------------------------------------------------------------
     */
    private function normalizeData(): void
    {
        $this->merge([
            'employee_id' => strtoupper(trim((string) $this->employee_id)),
            'email' => strtolower(trim((string) $this->email)),
            'salary' => str_replace([',', ' '], '', (string) $this->salary),
        ]);
    }

    private function employeeId(): ?int
    {
        return $this->route('employee')?->id;
    }
}