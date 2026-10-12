<?php

namespace App\Enums;

enum ProjectAssignmentEndReason: string
{
    case PROJECT_COMPLETED = 'project_completed';
    case PROJECT_CANCELLED = 'project_cancelled';
    case REMOVED = 'removed';

    public function label(): string
    {
        return __('site.projects.end_reasons.'.$this->value);
    }
}
