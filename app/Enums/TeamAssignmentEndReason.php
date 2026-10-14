<?php

namespace App\Enums;

enum TeamAssignmentEndReason: string
{
    case REMOVED = 'removed';
    case TRANSFERRED = 'transferred';
    case TEAM_DELETED = 'team_deleted';
    case EMPLOYEE_RESIGNED = 'employee_resigned';
    case EMPLOYEE_INACTIVATED = 'employee_inactivated';
    case EMPLOYEE_TERMINATED = 'employee_terminated';
    case EMPLOYEE_DELETED = 'employee_deleted';

    public function label(): string
    {
        return __('site.teams.end_reasons.'.$this->value);
    }
}
