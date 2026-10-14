<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ConvertsDisplayDates;
use Illuminate\Foundation\Http\FormRequest;

class RemoveAssignmentRequest extends FormRequest
{
    use ConvertsDisplayDates;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'end_date' => ['required', 'date_format:d/m/Y', 'before_or_equal:today'],
            'end_reason_note' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'end_date' => __('site.teams.end_date'),
            'end_reason_note' => __('site.teams.end_reason_note'),
        ];
    }

    protected function displayDateAttributes(): array
    {
        return ['end_date'];
    }
}
