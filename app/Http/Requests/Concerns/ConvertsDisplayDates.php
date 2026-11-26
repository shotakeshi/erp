<?php

namespace App\Http\Requests\Concerns;

use Carbon\Carbon;

/**
 * Convert display-formatted date fields (d/m/Y) to Y-m-d in the validated data.
 */
trait ConvertsDisplayDates
{
    /**
     * @return list<string>
     */
    abstract protected function displayDateAttributes(): array;

    public function validated($key = null, $default = null): mixed
    {
        $validated = parent::validated();

        foreach ($this->displayDateAttributes() as $dateAttribute) {
            if (! isset($validated[$dateAttribute])) {
                continue;
            }

            $validated[$dateAttribute] = Carbon::createFromFormat('d/m/Y', $validated[$dateAttribute])->toDateString();
        }

        return data_get($validated, $key, $default);
    }
}
