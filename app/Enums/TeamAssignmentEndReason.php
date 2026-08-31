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
        return match ($this) {
            self::REMOVED => 'Removed',
            self::TRANSFERRED => 'Transferred',
            self::TEAM_DELETED => 'Team deleted',
            self::EMPLOYEE_RESIGNED => 'Employee resigned',
            self::EMPLOYEE_INACTIVATED => 'Employee inactivated',
            self::EMPLOYEE_TERMINATED => 'Employee terminated',
            self::EMPLOYEE_DELETED => 'Employee deleted',
        };
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->map(fn (self $reason): array => [
                'value' => $reason->value,
                'label' => $reason->label(),
            ])
            ->values()
            ->all();
    }
}
