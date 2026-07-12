<?php

namespace App\Enums;

enum AgentTaskStatus: string
{
    case Pending = 'pending';
    case InProgress = 'in_progress';
    case Review = 'review';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::InProgress => 'In Progress',
            self::Review => 'Review',
            self::Completed => 'Completed',
            self::Cancelled => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'gray',
            self::InProgress => 'blue',
            self::Review => 'yellow',
            self::Completed => 'green',
            self::Cancelled => 'red',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->map(fn (self $status) => [
            'value' => $status->value,
            'label' => $status->label(),
            'color' => $status->color(),
        ])->values()->toArray();
    }

    /**
     * Get all status values as a flat array.
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}