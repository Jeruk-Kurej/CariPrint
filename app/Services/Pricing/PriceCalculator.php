<?php

namespace App\Services\Pricing;

use App\Enums\ColorMode;
use App\Enums\PriceUnit;
use App\Enums\ShopService;
use App\Models\PriceList;
use App\Models\Shop;

/**
 * All money logic lives here, read from the shop's price list. The AI never computes prices (core rule 1).
 */
class PriceCalculator
{
    public function forShop(Shop $shop, PrintSpec $spec): PriceBreakdown
    {
        $lines = [];
        $missing = [];

        if ($spec->bwPages > 0) {
            $this->addPageLine($shop, $spec, ColorMode::BlackWhite, $spec->bwPages, 'Hitam-putih', $lines, $missing);
        }

        if ($spec->colorPages > 0) {
            $this->addPageLine($shop, $spec, ColorMode::Color, $spec->colorPages, 'Warna', $lines, $missing);
        }

        if ($spec->binding !== null) {
            $price = $this->find($shop, $spec->binding, PriceUnit::Item, null, null);

            $price === null
                ? $missing[] = $spec->binding->value
                : $lines[] = new PriceLine($spec->binding->name, $price->price, $spec->copies);
        }

        return new PriceBreakdown($lines, $missing);
    }

    /**
     * @param  list<PriceLine>  $lines
     * @param  list<string>  $missing
     */
    private function addPageLine(Shop $shop, PrintSpec $spec, ColorMode $mode, int $pages, string $label, array &$lines, array &$missing): void
    {
        $price = $this->find($shop, ShopService::Print, PriceUnit::Page, $spec->paperSize, $mode);

        if ($price === null) {
            $missing[] = ShopService::Print->value.'-'.$mode->value;

            return;
        }

        $lines[] = new PriceLine("$label ({$spec->paperSize->value})", $price->price, $pages * $spec->copies);
    }

    private function find(Shop $shop, ShopService $service, PriceUnit $unit, $paper, ?ColorMode $mode): ?PriceList
    {
        return $shop->priceLists
            ->first(fn (PriceList $p) => $p->service === $service
                && $p->unit === $unit
                && ($paper === null || $p->paper_size === $paper)
                && ($mode === null || $p->color_mode === $mode));
    }
}
