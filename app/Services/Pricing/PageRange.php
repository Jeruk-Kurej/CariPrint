<?php

namespace App\Services\Pricing;

use App\Enums\ColorMode;

/**
 * One run of pages the user pointed at. Positions count from 1, and a negative position counts from the end,
 * so -1 is the last page. A null colour mode means these pages are left out, unless $auto is set: then each page
 * is printed in colour only if the browser found colour on it, and in black and white otherwise.
 */
readonly class PageRange
{
    public function __construct(
        public int $from,
        public int $to,
        public ?ColorMode $colorMode,
        public bool $auto = false,
    ) {}
}
