<?php

namespace App\Enums;

enum ProjectStatus: string
{
    case PLANNING = 'planning';
    case ACTIVE = 'active';
    case ON_HOLD = 'on_hold';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return __('site.projects.statuses.'.$this->value);
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::PLANNING => 'badge badge-soft-info',
            self::ACTIVE => 'badge badge-soft-success',
            self::ON_HOLD => 'badge badge-soft-warning',
            self::COMPLETED => 'badge badge-soft-primary',
            self::CANCELLED => 'badge badge-soft-danger',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $status) => [$status->value => $status->label()])
            ->toArray();
    }
}
