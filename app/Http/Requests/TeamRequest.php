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
        return [
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
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => __('site.teams.name'),
            'code' => __('site.teams.code'),
            'logo' => __('site.teams.logo'),
            'remove_logo' => __('site.teams.remove_logo'),
            'description' => __('site.teams.description'),
        ];
    }
}
