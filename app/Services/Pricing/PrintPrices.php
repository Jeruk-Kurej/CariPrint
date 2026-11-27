<?php

namespace App\Services\Pricing;

use App\Enums\PriceSource;
use App\Enums\ShopService;
use App\Models\PriceList;
use App\Models\Shop;
use Illuminate\Support\Collection;

/**
 * Cheapest print price of a shop for every paper size and colour choice a student can filter on, so the browser only
 * looks a price up and never works one out. Other services are ignored: a cheap scan must not make a shop look
 * cheapest for printing.
 */
class PrintPrices
{
    private const ANY = 'any';

    /**
     * Keys are "{paper}_{colour}", where either side may be "any". Choices the shop has no price for are left out.
     *
     * @return array<string, array{price: int, source: string}>
     */
    public function byChoice(Shop $shop): array
    {
        $choices = [];

        foreach ($this->cheapestFirst($shop) as $price) {
            foreach (array_unique([self::ANY, $price->paper_size->value ?? self::ANY]) as $paper) {
                foreach (array_unique([self::ANY, $price->color_mode->value ?? self::ANY]) as $color) {
                    $choices["{$paper}_{$color}"] ??= ['price' => $price->price, 'source' => $price->source->value];
                }
            }
        }

        return $choices;
    }

    /**
     * On equal prices the verified one comes first.
     *
     * @return Collection<int, PriceList>
     */
    private function cheapestFirst(Shop $shop): Collection
    {
        return $shop->priceLists
            ->where('service', ShopService::Print)
            ->sortBy(fn (PriceList $price): array => [$price->price, $price->source === PriceSource::Verified ? 0 : 1])
            ->values();
    }
}
