<?php

namespace App\Http\Requests;

use App\Enums\ContractType;
use App\Enums\Gender;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Carbon\Carbon;

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

            'remove_avatar' => [
                'nullable',
                'boolean',
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
                'required',
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
                'integer',
                Rule::exists('employees', 'id')
                    ->whereNull('deleted_at'),
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
                'required',
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

    public function validated($key = null, $default = null): mixed
    {
        $validated = parent::validated();

        foreach (['dob', 'date_of_joining'] as $dateAttribute) {
            if (! isset($validated[$dateAttribute])) {
                continue;
            }

            $validated[$dateAttribute] = Carbon::createFromFormat(
                'd/m/Y',
                $validated[$dateAttribute]
            )->format('Y-m-d');
        }

        return data_get($validated, $key, $default);
    }

    public function attributes(): array
    {
        return [
            'avatar' => __('validation.attributes.avatar'),
            'employee_id' => __('validation.attributes.employee_id'),
            'first_name' => __('validation.attributes.first_name'),
            'last_name' => __('validation.attributes.last_name'),
            'email' => __('validation.attributes.email'),
            'phone' => __('validation.attributes.phone'),
            'dob' => __('validation.attributes.dob'),
            'gender' => __('validation.attributes.gender'),
            'address' => __('validation.attributes.address'),
            'portal_code' => __('validation.attributes.portal_code'),
            'city' => __('validation.attributes.city'),
            'state' => __('validation.attributes.state'),
            'department_id' => __('validation.attributes.department_id'),
            'position_id' => __('validation.attributes.position_id'),
            'reporting_manager_id' => __('validation.attributes.reporting_manager_id'),
            'date_of_joining' => __('validation.attributes.date_of_joining'),
            'contract_type' => __('validation.attributes.contract_type'),
            'salary' => __('validation.attributes.salary'),
            'remove_avatar' => __('validation.attributes.remove_avatar'),
        ];
    }

    private function employeeId(): ?int
    {
        return $this->route('employee')?->id;
    }
}
