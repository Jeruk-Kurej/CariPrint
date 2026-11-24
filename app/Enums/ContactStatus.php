<?php

namespace App\Enums;

enum ContactStatus: string
{
    case New = 'new';
    case Replied = 'replied';
    case Done = 'done';

    public function label(): string
    {
        return match ($this) {
            self::New => 'Baru',
            self::Replied => 'Sudah dibalas',
            self::Done => 'Selesai',
        };
    }
}
