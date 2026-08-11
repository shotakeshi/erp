<?php

namespace App\Enums;

enum ContractType: string
{
    case PERMANENT = 'Permanent';
    case CONTRACT = 'Contract';
    case TEMPORARY = 'Temporary';

    public function label(): string
    {
        return match ($this) {
            self::PERMANENT => __('site.employees.contract_types.permanent'),
            self::CONTRACT => __('site.employees.contract_types.contract'),
            self::TEMPORARY => __('site.employees.contract_types.temporary'),
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $gender) => [
                $gender->value => $gender->label(),
            ])
            ->toArray();
    }
}