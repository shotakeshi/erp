<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class RemoveAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'end_date' => ['required', 'date', 'before_or_equal:today'],
        ];
    }

    public function attributes(): array
    {
        return [
            'end_date' => __('site.teams.end_date'),
            'end_date.before_or_equal' => __('site.teams.validation.date_cannot_be_future'),
        ];
    }
}
