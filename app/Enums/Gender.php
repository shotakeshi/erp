<?php

namespace App\Enums;

enum Gender: string
{
    case MALE = 'Male';
    case FEMALE = 'Female';
    case OTHER = 'Other';

    public function label(): string
    {
        return match ($this) {
            self::MALE   => __('site.employees.gender.male'),
            self::FEMALE => __('site.employees.gender.female'),
            self::OTHER  => __('site.employees.gender.other'),
        };
    }


    public function badgeClass(): string
    {
        return match ($this) {
            self::MALE   => 'badge bg-primary',
            self::FEMALE => 'badge bg-pink',
            self::OTHER  => 'badge bg-secondary',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::MALE   => 'ti ti-gender-male',
            self::FEMALE => 'ti ti-gender-female',
            self::OTHER  => 'ti ti-gender-bigender',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::MALE   => 'primary',
            self::FEMALE => 'pink',
            self::OTHER  => 'secondary',
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