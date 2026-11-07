<?php

namespace Database\Seeders;

use App\Enums\ColorMode;
use App\Enums\PaperSize;
use App\Enums\PriceSource;
use App\Enums\PriceUnit;
use App\Enums\ShopService;
use App\Models\Shop;
use Illuminate\Database\Seeder;

class ShopSeeder extends Seeder
{
    /**
     * Sample shops with estimated prices, used until the survey data arrives (task 5.6).
     */
    public function run(): void
    {
        $weekday = [['open' => '08:00', 'close' => '21:00']];

        $samples = [
            ['slug' => 'contoh-print-a', 'name' => 'Contoh Print A', 'latitude' => -7.2875310, 'longitude' => 112.6370120, 'price' => 500],
            ['slug' => 'contoh-print-b', 'name' => 'Contoh Print B', 'latitude' => -7.2862000, 'longitude' => 112.6402000, 'price' => 700],
        ];

        foreach ($samples as $sample) {
            $shop = Shop::updateOrCreate(['slug' => $sample['slug']], [
                'name' => $sample['name'],
                'address' => 'CitraLand, Surabaya',
                'latitude' => $sample['latitude'],
                'longitude' => $sample['longitude'],
                'whatsapp' => null,
                'opening_hours' => [
                    'mon' => $weekday, 'tue' => $weekday, 'wed' => $weekday,
                    'thu' => $weekday, 'fri' => $weekday, 'sat' => $weekday,
                    'sun' => [],
                ],
                'services' => [ShopService::Print, ShopService::Photocopy],
            ]);

            $shop->priceLists()->delete();
            $shop->priceLists()->create([
                'service' => ShopService::Print,
                'paper_size' => PaperSize::A4,
                'color_mode' => ColorMode::BlackWhite,
                'price' => $sample['price'],
                'unit' => PriceUnit::Page,
                'source' => PriceSource::Estimated,
            ]);
        }
    }
}
