<?php

namespace App\Services\Pricing;

use RuntimeException;

/**
 * A print request that cannot be turned into a PrintSpec. The message is Indonesian UI copy, shown to the user as is.
 */
class UnreadablePrintRequest extends RuntimeException
{
    public static function assistantUnavailable(): self
    {
        return new self('Asisten belum bisa dipakai saat ini. Coba lagi sebentar lagi.');
    }

    /**
     * The request points at pages from the end, but nobody said how long the document is.
     */
    public static function lengthUnknown(): self
    {
        return new self('Asisten perlu tahu jumlah halaman dokumenmu. Lampirkan file PDF-nya atau tulis jumlah halamannya.');
    }

    public static function outsideDocument(int $totalPages): self
    {
        $total = number_format($totalPages, 0, ',', '.');

        return new self("Dokumenmu {$total} halaman, tetapi permintaanmu menyebut halaman di luar itu. Periksa lagi nomor halamannya.");
    }

    public static function overLimit(int $maxPages): self
    {
        $max = number_format($maxPages, 0, ',', '.');

        return new self("Asisten hanya bisa menghitung halaman 1 sampai {$max}. Periksa lagi nomor halamannya.");
    }
}
