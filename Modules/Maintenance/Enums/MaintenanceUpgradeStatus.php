<?php

namespace Modules\Maintenance\Enums;

enum MaintenanceUpgradeStatus: string
{
    case PENDING = 'pending';
    case WAITING_APPROVAL = 'waiting_approval';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';
    case PAID = 'paid';
    case STARTED = 'started';
    case COMPLETED = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'قيد الانتظار',
            self::WAITING_APPROVAL => 'في انتظار الموافقة',
            self::ACCEPTED => 'مقبول',
            self::REJECTED => 'مرفوض',
            self::PAID => 'مدفوع',
            self::STARTED => 'بدء الترقية',
            self::COMPLETED => 'تم الترقية',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => '#FFA07A',        // Light Salmon
            self::WAITING_APPROVAL => '#87CEFA', // Light Sky Blue
            self::ACCEPTED => '#98FB98',       // Pale Green
            self::REJECTED => '#F08080',       // Light Coral
            self::PAID => '#DDA0DD',           // Plum
            self::STARTED => '#FFD700',        // Gold
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