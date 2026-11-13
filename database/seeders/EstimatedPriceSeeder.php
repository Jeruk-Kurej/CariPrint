<?php

namespace Database\Seeders;

use App\Enums\ColorMode;
use App\Enums\PaperSize;
use App\Enums\PriceSource;
use App\Enums\PriceUnit;
use App\Enums\ShopService;
use App\Models\Shop;
use Illuminate\Database\Seeder;

/**
 * Fills shops that have no real prices yet with invented sample prices, so the comparison page and the print
 * assistant can be demonstrated. Every row is stored with source "demo" and shown to visitors as "Data contoh".
 *
 * Run: php artisan db:seed --class=DemoPriceSeeder
 * Remove: php artisan tinker --execute 'App\Models\PriceList::where("source", "demo")->delete();'
 */
class DemoPriceSeeder extends Seeder
{
    private const SPREAD_STEPS = 5;

    private const ROUND_TO = 50;

    public function run(): void
    {
        foreach (Shop::query()->with('priceLists')->get() as $shop) {
            $hasRealPrices = $shop->priceLists->contains(fn ($price) => $price->source !== PriceSource::Demo);

            if ($hasRealPrices) {
                continue;
            }

            $shop->priceLists()->where('source', PriceSource::Demo->value)->delete();

            foreach ($this->basePrices() as [$service, $paperSize, $colorMode, $base]) {
                if (! $shop->services->contains($service)) {
                    continue;
                }

                $shop->priceLists()->create([
                    'service' => $service,
                    'paper_size' => $paperSize,
                    'color_mode' => $colorMode,
                    'price' => $this->priceFor($shop, "{$service->value}|{$paperSize->value}|{$colorMode->value}", $base),
                    'unit' => PriceUnit::Page,
                    'source' => PriceSource::Demo,
                ]);
            }
        }
    }

    /**
     * Typical student-print prices in whole Rupiah per page.
     *
     * @return list<array{0: ShopService, 1: PaperSize, 2: ColorMode, 3: int}>
     */
    private function basePrices(): array
    {
        return [
            [ShopService::Print, PaperSize::A4, ColorMode::BlackWhite, 500],
            [ShopService::Print, PaperSize::A4, ColorMode::Color, 2000],
            [ShopService::Print, PaperSize::F4, ColorMode::BlackWhite, 600],
            [ShopService::Print, PaperSize::F4, ColorMode::Color, 2500],
            [ShopService::Print, PaperSize::A3, ColorMode::BlackWhite, 2000],
            [ShopService::Print, PaperSize::A3, ColorMode::Color, 5000],
            [ShopService::Photocopy, PaperSize::A4, ColorMode::BlackWhite, 250],
        ];
    }

    /**
     * Spreads the base price between 80% and 120% so the cheapest shop differs from row to row. The spread comes
     * from a hash of the shop and the row, so running the seeder again gives the same prices.
     */
    private function priceFor(Shop $shop, string $row, int $base): int
    {
        $step = crc32("{$shop->slug}|{$row}") % self::SPREAD_STEPS;
        $percent = 80 + $step * 10;

        return (int) (round($base * $percent / 100 / self::ROUND_TO) * self::ROUND_TO);
    }
}
