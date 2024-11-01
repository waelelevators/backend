<?php

namespace Modules\Maintenance\Enums;

enum MaintenanceVisitsStatus: string
{
    case SCHEDULED = 'scheduled';
    case COMPLETED = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::SCHEDULED => 'مجدول',
            self::COMPLETED => 'مكتمل',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::SCHEDULED => '#FFA07A',        // Light Salmon
            self::COMPLETED => '#20B2AA',      // Light Sea Green
        };
    }

    public function toArray(): array
    {
        return [
            'value' => $this->value,
            'label' => $this->label(),
            'color' => $this->color(),
        ];
    }
}