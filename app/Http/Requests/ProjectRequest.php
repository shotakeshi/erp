<?php

namespace App\Http\Requests;

use App\Enums\ProjectPriority;
use App\Enums\ProjectStatus;
use App\Enums\ProjectType;
use App\Http\Requests\Concerns\ConvertsDisplayDates;
use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class ProjectRequest extends FormRequest
{
    use ConvertsDisplayDates;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'code' => Str::upper(trim((string) $this->input('code'))),
        ]);
    }

    public function rules(): array
    {
        $project = $this->route('project');
        $managerId = (int) $this->input('manager_id');
        $teamId = (int) $this->input('team_id');
        $managerExistsRule = Rule::exists('employees', 'id')->whereNull('deleted_at');
        $teamExistsRule = Rule::exists('teams', 'id')->whereNull('deleted_at');

        if ($project instanceof Project && $managerId === $project->manager_id) {
            $managerExistsRule = Rule::exists('employees', 'id');
        }

        if ($project instanceof Project && $teamId === $project->team_id) {
            $teamExistsRule = Rule::exists('teams', 'id');
        }

        $rules = [
            'project_avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_project_avatar' => ['nullable', 'boolean'],
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique(Project::class, 'code')->ignore($this->route('project')),
            ],
            'project_type' => ['required', new Enum(ProjectType::class)],
            'description' => ['nullable', 'string', 'max:5000'],
            'status' => ['required', new Enum(ProjectStatus::class)],
            'priority' => ['required', new Enum(ProjectPriority::class)],
            'start_date' => ['nullable', 'date_format:d/m/Y'],
            'planned_end_date' => ['nullable', 'date_format:d/m/Y', 'after_or_equal:start_date'],
            'actual_end_date' => ['nullable', 'date_format:d/m/Y', 'after_or_equal:start_date'],
            'manager_id' => [
                'nullable',
                'integer',
                $managerExistsRule,
            ],
            'team_id' => [
                'nullable',
                'integer',
                $teamExistsRule,
            ],
            'department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'project_url' => ['nullable', 'url', 'max:2048'],
        ];

        if ($this->isMethod('post')) {
            $rules += [
                'members' => ['nullable', 'array'],
                'members.*' => ['required', 'array:employee_id,role'],
                'members.*.employee_id' => ['required', 'integer', 'distinct', 'exists:employees,id'],
                'members.*.role' => ['required', 'string', 'max:255'],
            ];
        }

        return $rules;
    }

    protected function displayDateAttributes(): array
    {
        return ['start_date', 'planned_end_date', 'actual_end_date'];
    }

    public function attributes(): array
    {
        return [
            'project_avatar' => __('site.projects.project_avatar'),
            'remove_project_avatar' => __('site.projects.remove_project_avatar'),
            'name' => __('site.projects.name'),
            'code' => __('site.projects.code'),
            'project_type' => __('site.projects.project_type'),
            'description' => __('site.projects.description'),
            'status' => __('site.projects.status'),
            'priority' => __('site.projects.priority'),
            'start_date' => __('site.projects.start_date'),
            'planned_end_date' => __('site.projects.planned_end_date'),
            'actual_end_date' => __('site.projects.actual_end_date'),
            'manager_id' => __('site.projects.manager'),
            'team_id' => __('site.projects.team'),
            'department_id' => __('site.projects.department'),
            'project_url' => __('site.projects.project_url'),
            'members' => __('site.projects.members'),
            'members.*.employee_id' => __('site.projects.employee'),
            'members.*.role' => __('site.projects.role'),
        ];
    }
}
