<?php

namespace App\Enums;

enum UserStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case ON_LEAVE = 'onleave';
    case TERMINATED = 'terminated';
    case BLOCKED = 'blocked';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE      => 'Active',
            self::INACTIVE    => 'Inactive',
            self::ON_LEAVE    => 'On Leave',
            self::TERMINATED  => 'Terminated',
            self::BLOCKED     => 'Blocked',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::ACTIVE      => 'badge badge-pill badge-success pl-2 pr-2',
            self::INACTIVE    => 'badge badge-pill badge-danger pl-2 pr-2',
            self::ON_LEAVE    => 'badge badge-pill badge-warning pl-2 pr-2',
            self::TERMINATED  => 'badge badge-pill badge-danger pl-2 pr-2',
            self::BLOCKED     => 'badge badge-pill badge-dark pl-2 pr-2',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::ACTIVE      => 'ti ti-unlock',
            self::INACTIVE    => 'ti ti-close',
            self::ON_LEAVE    => 'ti ti-unlink',
            self::TERMINATED  => 'ti ti-line-dotted',
            self::BLOCKED     => 'ti ti-lock',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ACTIVE      => 'success',
            self::INACTIVE    => 'secondary',
            self::ON_LEAVE    => 'warning',
            self::TERMINATED  => 'danger',
            self::BLOCKED     => 'dark',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->map(fn (self $status) => [
                'value' => $status->value,
                'label' => $status->label(),
            ])
            ->values()
            ->all();
    }
}