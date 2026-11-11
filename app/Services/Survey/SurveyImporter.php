<?php

namespace App\Services\Survey;

use App\Enums\ColorMode;
use App\Enums\PaperSize;
use App\Enums\PriceSource;
use App\Enums\PriceUnit;
use App\Enums\ShopService;
use App\Models\PriceList;
use App\Models\Shop;
use Carbon\CarbonImmutable;
use InvalidArgumentException;

/**
 * Imports the team's survey CSVs (format in docs/SURVEY.md). Re-running it updates rows, never duplicates them.
 */
class SurveyImporter
{
    private const DAYS = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];

    public function import(string $shopsCsv, string $pricesCsv): int
    {
        $shops = CsvFile::read($shopsCsv);
        $prices = CsvFile::read($pricesCsv);

        foreach ($shops as $row) {
            $shop = Shop::updateOrCreate(['slug' => $row['slug']], [
                'name' => $row['name'],
                'address' => $row['address'],
                'latitude' => (float) $row['latitude'],
                'longitude' => (float) $row['longitude'],
                'whatsapp' => $row['whatsapp'] ?: null,
                'place_id' => ($row['place_id'] ?? '') ?: null,
                'opening_hours' => $this->openingHours($row),
                'services' => $this->services($row['services']),
                'color_page_rule' => $row['color_page_rule'] ?: null,
                'last_surveyed_at' => CarbonImmutable::parse($row['surveyed_at'], 'Asia/Jakarta'),
            ]);

            $shop->priceLists()->delete();
            foreach (array_filter($prices, fn (array $p) => $p['shop_slug'] === $row['slug']) as $price) {
                $this->createPrice($shop, $price);
            }
        }

        return count($shops);
    }

    /**
     * @param  array<string, string>  $row
     * @return array<string, list<array{open: string, close: string}>>
     */
    private function openingHours(array $row): array
    {
        $hours = [];

        foreach (self::DAYS as $day) {
            $value = trim($row[$day] ?? '');
            $hours[$day] = ($value === '' || $value === 'tutup') ? [] : array_map(function (string $slot) {
                [$open, $close] = explode('-', $slot, 2) + [1 => ''];

                return ['open' => trim($open), 'close' => trim($close)];
            }, explode('|', $value));
        }

        return $hours;
    }

    /**
     * @return list<ShopService>
     */
    private function services(string $codes): array
    {
        return array_map(function (string $code) {
            return ShopService::tryFrom(trim($code))
                ?? throw new InvalidArgumentException("Kode layanan tidak dikenal: $code");
        }, array_values(array_filter(explode('|', $codes))));
    }

    /**
     * @param  array<string, string>  $row
     */
    private function createPrice(Shop $shop, array $row): void
    {
        $shop->priceLists()->save(new PriceList([
            'service' => ShopService::tryFrom($row['service']) ?? throw new InvalidArgumentException("Kode layanan tidak dikenal: {$row['service']}"),
            'paper_size' => $row['paper_size'] !== '' ? PaperSize::from($row['paper_size']) : null,
            'color_mode' => $row['color_mode'] !== '' ? ColorMode::from($row['color_mode']) : null,
            'price' => (int) $row['price'],
            'unit' => PriceUnit::from($row['unit']),
            'source' => PriceSource::Verified,
        ]));
    }
}
