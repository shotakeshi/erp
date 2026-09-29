<?php

namespace App\Enums;

enum ProjectType: string
{
    case DEVELOPMENT = 'development';
    case MAINTENANCE = 'maintenance';
    case INTERNAL = 'internal';
    case SUPPORT = 'support';

    public function label(): string
    {
        return match ($this) {
            self::DEVELOPMENT => 'Development',
            self::MAINTENANCE => 'Maintenance',
            self::INTERNAL => 'Internal',
            self::SUPPORT => 'Support',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $type) => [$type->value => $type->label()])
            ->toArray();
    }
}
