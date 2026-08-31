<?php

namespace App\Enums;

enum TeamAssignmentRole: string
{
    case MEMBER = 'member';
    case MANAGER = 'manager';

    public function label(): string
    {
        return match ($this) {
            self::MEMBER => 'Member',
            self::MANAGER => 'Manager',
        };
    }
}
