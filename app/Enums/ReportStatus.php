<?php

namespace App\Enums;

enum ReportStatus: string
{
    case Pending = 'pending';
    case Resolved = 'resolved';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu',
            self::Resolved => 'Selesai',
            self::Rejected => 'Ditolak',
        };
    }
}
