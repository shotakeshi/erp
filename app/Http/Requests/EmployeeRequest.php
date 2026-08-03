<?php

namespace App\Http\Requests;

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

    public function messages(): array
    {
        return [
            'required' => ':attribute là bắt buộc.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email đã tồn tại.',
            'employee_id.unique' => 'Mã nhân viên đã tồn tại.',

            'avatar.image' => 'Avatar phải là hình ảnh.',
            'avatar.mimes' => 'Avatar chỉ được phép jpg, jpeg, png, webp.',
            'avatar.max' => 'Avatar tối đa 2MB.',

            'salary.numeric' => 'Lương phải là số.',
            'salary.min' => 'Lương phải lớn hơn hoặc bằng 0.',

            'dob.date_format' => 'Ngày sinh phải có định dạng dd/mm/yyyy.',
            'date_of_joining.date_format' => 'Ngày vào làm phải có định dạng dd/mm/yyyy.',
        ];
    }

    public function attributes(): array
    {
        return [
            'avatar' => 'Avatar',
            'employee_id' => 'Mã nhân viên',
            'first_name' => 'Tên',
            'last_name' => 'Họ',
            'email' => 'Email',
            'phone' => 'Số điện thoại',
            'dob' => 'Ngày sinh',
            'gender' => 'Giới tính',
            'address' => 'Địa chỉ',
            'portal_code' => 'Mã bưu điện',
            'city' => 'Tỉnh / Thành phố',
            'state' => 'Quận / Huyện',
            'department_id' => 'Phòng ban',
            'position_id' => 'Chức vụ',
            'reporting_manager_id' => 'Quản lý',
            'date_of_joining' => 'Ngày vào làm',
            'contract_type' => 'Loại hợp đồng',
            'salary' => 'Lương',
        ];
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
                'required',
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
                'string',
                'max:50',
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