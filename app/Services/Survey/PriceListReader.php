<?php

namespace App\Services\Survey;

use App\Ai\Agents\PriceListReaderAgent;
use App\Enums\ShopService;
use Laravel\Ai\Files\Image;

/**
 * Reads price-list photos into draft survey rows. Every row that the AI was unsure about, or that is missing
 * a field the price calculator needs, is marked for a person to check before the row is imported.
 */
class PriceListReader
{
    private const PAGE_SERVICES = [ShopService::Print, ShopService::Photocopy, ShopService::Scan];

    private const NEEDS_PAPER = [ShopService::Print, ShopService::Photocopy];

    private const MAX_PRICE = 500_000;

    /**
     * @param  list<string>  $photoPaths
     * @return list<array<string, string>>
     */
    public function read(string $shopSlug, array $photoPaths): array
    {
        $rows = [];

        foreach ($photoPaths as $path) {
            $result = (new PriceListReaderAgent)->prompt(
                'Baca daftar harga pada foto ini.',
                attachments: [Image::fromPath($path)],
            );

            foreach ($result['items'] ?? [] as $item) {
                $row = $this->row($shopSlug, $item, basename($path));

                if ($row !== null) {
                    $rows[] = $row;
                }
            }
        }

        return $this->flagConflicts($rows);
    }

    /**
     * @param  array<string, mixed>  $item
     * @return array<string, string>|null
     */
    private function row(string $shopSlug, array $item, string $photo): ?array
    {
        $service = ShopService::tryFrom((string) ($item['service'] ?? ''));

        if ($service === null) {
            return null;
        }

        $price = (int) ($item['price'] ?? 0);
        $paper = in_array($service, self::NEEDS_PAPER, true) || $service === ShopService::Scan ? ($item['paper_size'] ?? null) : null;
        $color = in_array($service, self::PAGE_SERVICES, true) ? ($item['color_mode'] ?? null) : null;

        $check = ! ($item['confident'] ?? false)
            || $price <= 0
            || $price > self::MAX_PRICE
            || (in_array($service, self::NEEDS_PAPER, true) && ($paper === null || $color === null));

        return [
            'shop_slug' => $shopSlug,
            'service' => $service->value,
            'paper_size' => (string) $paper,
            'color_mode' => (string) $color,
            'price' => (string) $price,
            'unit' => in_array($service, self::PAGE_SERVICES, true) ? 'page' : 'item',
            'tulisan_di_foto' => (string) ($item['text'] ?? ''),
            'periksa' => $check ? 'ya' : '',
            'foto' => $photo,
        ];
    }

    /**
     * The same service, paper and colour with different prices across photos is flagged on every such row.
     *
     * @param  list<array<string, string>>  $rows
     * @return list<array<string, string>>
     */
    private function flagConflicts(array $rows): array
    {
        $prices = [];

        foreach ($rows as $row) {
            $prices[$this->key($row)][$row['price']] = true;
        }

        return array_map(function (array $row) use ($prices) {
            if (count($prices[$this->key($row)]) > 1) {
                $row['periksa'] = 'ya';
            }

            return $row;
        }, $rows);
    }

    /**
     * @param  array<string, string>  $row
     */
    private function key(array $row): string
    {
        return implode('|', [$row['shop_slug'], $row['service'], $row['paper_size'], $row['color_mode']]);
    }
}
