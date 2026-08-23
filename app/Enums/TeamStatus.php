<?php

namespace App\Enums;

enum TeamStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
        };
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->map(fn (self $status): array => [
                'value' => $status->value,
                'label' => $status->label(),
            ])
            ->values()
            ->all();
    }
}
