<?php

namespace App\Services\Pricing;

use App\Enums\ColorMode;
use App\Enums\PaperSize;
use App\Enums\ShopService;
use App\Models\PriceList;
use App\Models\Shop;
use Illuminate\Support\Collection;

/**
 * Lines up every shop's price for the same service so students can compare them. The cheapest cell per row is
 * decided here, never in the browser or by AI (core rule 1).
 */
class PriceComparison
{
    /**
     * @return array{shops: array<int, array{id: int, name: string, slug: string}>, rows: list<array<string, mixed>>, hasDemoPrices: bool}
     */
    public function forAllShops(): array
    {
        return $this->build(Shop::query()->with('priceLists')->orderBy('name')->get());
    }

    /**
     * @param  Collection<int, Shop>  $shops
     * @return array{shops: array<int, array{id: int, name: string, slug: string}>, rows: list<array<string, mixed>>, hasDemoPrices: bool}
     */
    public function build(Collection $shops): array
    {
        $shops = $shops->values();
        $rows = [];

        foreach ($shops as $column => $shop) {
            foreach ($shop->priceLists as $price) {
                $key = $this->key($price);
                $rows[$key] ??= ['price' => $price, 'prices' => array_fill(0, $shops->count(), null)];

                $current = $rows[$key]['prices'][$column];
                $rows[$key]['prices'][$column] = $current === null ? $price->price : min($current, $price->price);
            }
        }

        usort($rows, fn (array $a, array $b) => $this->order($a['price']) <=> $this->order($b['price']));

        return [
            'shops' => $shops->map(fn (Shop $shop) => [
                'id' => $shop->id,
                'name' => $shop->name,
                'slug' => $shop->slug,
            ])->all(),
            'hasDemoPrices' => $shops->contains(fn (Shop $shop) => $shop->hasDemoPrices()),
            'rows' => array_map(fn (array $row) => [
                'service' => $row['price']->service->name,
                'paperSize' => $row['price']->paper_size?->value,
                'colorMode' => $row['price']->color_mode?->value,
                'unit' => $row['price']->unit->value,
                'prices' => $row['prices'],
                'cheapest' => $this->cheapestColumns($row['prices']),
            ], $rows),
        ];
    }

    /**
     * @param  array<int, int|null>  $prices
     * @return list<int>
     */
    private function cheapestColumns(array $prices): array
    {
        $available = array_filter($prices, fn (?int $price) => $price !== null);

        if (count($available) < 2) {
            return [];
        }

        $lowest = min($available);

        return array_keys(array_filter($available, fn (int $price) => $price === $lowest));
    }

    private function key(PriceList $price): string
    {
        return implode('|', [
            $price->service->value,
            $price->paper_size?->value,
            $price->color_mode?->value,
            $price->unit->value,
        ]);
    }

    /**
     * @return array{int, int, int}
     */
    private function order(PriceList $price): array
    {
        return [
            (int) array_search($price->service, ShopService::cases(), true),
            $price->paper_size === null ? 99 : (int) array_search($price->paper_size, PaperSize::cases(), true),
            $price->color_mode === null ? 99 : (int) array_search($price->color_mode, ColorMode::cases(), true),
        ];
    }
}
