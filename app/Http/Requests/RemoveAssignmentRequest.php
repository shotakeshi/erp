<?php

namespace App\Http\Requests;

use Carbon\Carbon;
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
            'end_date' => ['required', 'date_format:d/m/Y' , 'before_or_equal:today'],
        ];
    }

    public function attributes(): array
    {
        return [
            'end_date' => __('site.teams.end_date'),
            'end_date.before_or_equal' => __('site.teams.validation.date_cannot_be_future'),
        ];
    }

    public function validated($key = null, $default = null): mixed
    {
        $validated = parent::validated();

        foreach (['end_date'] as $dateAttribute) {
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

}
