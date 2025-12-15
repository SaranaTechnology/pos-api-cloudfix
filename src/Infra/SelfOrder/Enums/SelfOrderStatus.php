<?php

namespace Infra\SelfOrder\Enums;

enum SelfOrderStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case PREPARING = 'preparing';
    case READY = 'ready';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu Konfirmasi',
            self::CONFIRMED => 'Dikonfirmasi',
            self::PREPARING => 'Sedang Disiapkan',
            self::READY => 'Siap Diambil',
            self::COMPLETED => 'Selesai',
            self::CANCELLED => 'Dibatalkan',
        };
    }
}
