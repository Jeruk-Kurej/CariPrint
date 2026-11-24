<?php

namespace App\Enums;

enum ContactCategory: string
{
    case Data = 'data';
    case Account = 'account';
    case Suggestion = 'suggestion';
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
            self::Data => 'Hapus atau ubah data',
            self::Account => 'Masalah akun',
            self::Suggestion => 'Saran',
            self::Other => 'Lainnya',
        };
    }
}
