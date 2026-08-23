<?php

namespace App\Enums;

enum TeamManagerEndReason: string
{
    case REPLACED = 'replaced'; // Có manager khác thay thế
    case TRANSFERRED = 'transferred'; // Manager chuyển team/department
    case RESIGNED = 'resigned'; // Employee tự nghỉ việc
    case TERMINATED = 'terminated'; // Công ty chấm dứt
    case REMOVED = 'removed'; // Bị gỡ khỏi vai trò manager
    case TEAM_DELETED = 'team_deleted'; // Team bị xóa

    public function label(): string
    {
        return match ($this) {
            self::REPLACED => 'Replaced',
            self::TRANSFERRED => 'Transferred',
            self::RESIGNED => 'Resigned',
            self::TERMINATED => 'Terminated',
            self::REMOVED => 'Removed',
            self::TEAM_DELETED => 'Team deleted',
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
