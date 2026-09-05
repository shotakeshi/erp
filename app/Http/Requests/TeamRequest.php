<?php

namespace App\Http\Requests;

use App\Models\Team;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TeamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'logo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
            'remove_logo' => [
                'nullable',
                'boolean',
            ],
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique(Team::class, 'code')->ignore($this->route('team')),
            ],
            'description' => ['nullable', 'string', 'max:5000'],
            'members' => ['nullable', 'array'],
            'members.*.employee_id' => [
                'required',
                'integer',
                'distinct',
                'exists:employees,id',
            ],
            'members.*.role' => ['required', 'string', 'max:50'],
            'members.*.is_manager' => ['required', 'boolean'],
        ];

        if ($this->route('team')) {
            unset($rules['members.*.employee_id'], $rules['members.*.is_manager']);
            $rules['members.*'] = ['required', 'array:assignment_id,role'];
            $rules['members.*.assignment_id'] = ['required', 'integer', 'distinct', 'exists:team_assignments,id'];
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'name' => __('site.teams.name'),
            'code' => __('site.teams.code'),
            'logo' => __('site.teams.logo'),
            'remove_logo' => __('site.teams.remove_logo'),
            'description' => __('site.teams.description'),
            'members' => __('site.teams.members'),
            'members.*.employee_id' => __('site.teams.employee'),
            'members.*.assignment_id' => __('site.teams.employee'),
            'members.*.role' => __('site.teams.role'),
            'members.*.is_manager' => __('site.teams.team_manager'),
        ];
    }
}
