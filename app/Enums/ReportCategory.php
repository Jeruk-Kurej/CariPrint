<?php

namespace App\Enums;

enum ReportCategory: string
{
    case Hours = 'hours';
    case Closed = 'closed';
    case Phone = 'phone';
    case Price = 'price';
    case Location = 'location';
    case Other = 'other';

    /**
     * @return list<array{value: string, label: string}>
     */
    public static function options(): array
    {
        return array_map(fn (self $category) => ['value' => $category->value, 'label' => $category->label()], self::cases());
    }

    public function label(): string
    {
        return match ($this) {
            self::Hours => 'Jam buka salah',
            self::Closed => 'Sudah tutup permanen',
            self::Phone => 'Nomor WhatsApp salah',
            self::Price => 'Harga tidak cocok',
            self::Location => 'Alamat atau lokasi salah',
            self::Other => 'Lainnya',
        };
    }
}
