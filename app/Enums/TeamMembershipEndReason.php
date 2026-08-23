<?php

namespace App\Enums;

enum TeamMembershipEndReason: string
{
    case MANUAL_REMOVE = 'manual_remove'; //remove employee khỏi team
    case TEAM_DELETED = 'team_deleted'; // Team bị xóa
    case TRANSFERRED = 'transferred'; // Employee chuyển từ team hiện tại sang team khác
    case EMPLOYEE_RESIGNED = 'employee_resigned'; // Employee tự nghỉ việc
    case EMPLOYEE_INACTIVATED = 'employee_inactivated'; // Employee chuyển sang inactive
    case EMPLOYEE_TERMINATED = 'employee_terminated'; // Employee bị chấm dứt hợp đồng
    case EMPLOYEE_DELETED = 'employee_deleted'; // Employee bị soft delete

    public function label(): string
    {
        return match ($this) {
            self::MANUAL_REMOVE => 'Manual remove',
            self::TEAM_DELETED => 'Team deleted',
            self::TRANSFERRED => 'Transferred',
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
