<?php

namespace App\Services\Pricing;

use App\Enums\PaperSize;
use App\Enums\ShopService;

/**
 * What the user wants printed. Produced from the user's text by PrintRequestReader,
 * then priced by PriceCalculator. Duplex does not change the price in this version.
 * $pages is set only when the user pointed at specific pages instead of giving a number of pages.
 */
readonly class PrintSpec
{
    public function __construct(
        public PaperSize $paperSize,
        public int $bwPages,
        public int $colorPages,
        public int $copies = 1,
        public ?ShopService $binding = null,
        public ?PageSelection $pages = null,
    ) {}
}
