<?php

namespace App\Enums;

enum ProjectMemberEndReason: string
{
    case PROJECT_COMPLETED = 'project_completed';
    case PROJECT_CANCELLED = 'project_cancelled';
    case REMOVED = 'removed';

    public function label(): string
    {
        return match ($this) {
            self::PROJECT_COMPLETED => 'Project completed',
            self::PROJECT_CANCELLED => 'Project cancelled',
            self::REMOVED => 'Removed',
        };
    }
}
