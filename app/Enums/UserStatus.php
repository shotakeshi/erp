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
            self::ACTIVE      => 'badge bg-success',
            self::INACTIVE    => 'badge bg-secondary',
            self::ON_LEAVE    => 'badge bg-warning',
            self::TERMINATED  => 'badge bg-danger',
            self::BLOCKED     => 'badge bg-dark',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::ACTIVE      => 'ti ti-check',
            self::INACTIVE    => 'ti ti-user-off',
            self::ON_LEAVE    => 'ti ti-beach',
            self::TERMINATED  => 'ti ti-user-x',
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
}