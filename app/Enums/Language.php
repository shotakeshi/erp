<?php

namespace App\Enums;

enum Language: string
{
    case VI = 'vi';
    case EN = 'en';
    case JA = 'ja';

    public function label(): string
    {
        return match ($this) {
            self::VI => 'Tiếng Việt',
            self::EN => 'English',
            self::JA => '日本語',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($item) => [$item->value => $item->label()])
            ->toArray();
    }
}
