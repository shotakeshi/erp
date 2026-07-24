<?php

namespace App\Enums;

enum UserStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case ON_LEAVE = 'onleave';
    case TERMINATED = 'terminated';
    case BLOCKED = 'blocked';
}