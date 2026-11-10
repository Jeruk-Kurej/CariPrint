<?php

namespace App\Services\Pricing;

use App\Enums\PaperSize;
use App\Enums\ShopService;

/**
 * What the user wants printed. Produced from the user's text by the AI agent (task 4.2),
 * then priced by PriceCalculator. Duplex does not change the price in this version.
 */
readonly class PrintSpec
{
    public function __construct(
        public PaperSize $paperSize,
        public int $bwPages,
        public int $colorPages,
        public int $copies = 1,
        public ?ShopService $binding = null,
    ) {}
}
