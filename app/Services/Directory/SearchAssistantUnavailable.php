<?php

namespace App\Services\Directory;

use RuntimeException;

/**
 * The AI could not be reached. The message is Indonesian UI copy, shown to the user as is.
 */
class SearchAssistantUnavailable extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Cari pakai AI belum bisa dipakai saat ini. Pakai pencarian dan filter biasa dulu.');
    }
}
