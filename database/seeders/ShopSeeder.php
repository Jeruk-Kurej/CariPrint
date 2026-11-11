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
     * Names start with "Contoh" so nobody mistakes them for real places.
     */
    public function run(): void
    {
        foreach ($this->samples() as $sample) {
            $shop = Shop::updateOrCreate(['slug' => $sample['slug']], [
                'name' => $sample['name'],
                'address' => 'CitraLand, Surabaya',
                'latitude' => $sample['latitude'],
                'longitude' => $sample['longitude'],
                'whatsapp' => null,
                'opening_hours' => $sample['hours'],
                'services' => collect($sample['prices'])->pluck(0)->unique()->values()->all(),
            ]);

            $shop->priceLists()->delete();

            foreach ($sample['prices'] as [$service, $paperSize, $colorMode, $price]) {
                $shop->priceLists()->create([
                    'service' => $service,
                    'paper_size' => $paperSize,
                    'color_mode' => $colorMode,
                    'price' => $price,
                    'unit' => $paperSize === null ? PriceUnit::Item : PriceUnit::Page,
                    'source' => PriceSource::Estimated,
                ]);
            }
        }
    }

    /**
     * @return list<array{slug: string, name: string, latitude: float, longitude: float, hours: array<string, list<array{open: string, close: string}>>, prices: list<array{0: ShopService, 1: ?PaperSize, 2: ?ColorMode, 3: int}>}>
     */
    private function samples(): array
    {
        $bw = fn (int $price, PaperSize $size = PaperSize::A4) => [ShopService::Print, $size, ColorMode::BlackWhite, $price];
        $color = fn (int $price, PaperSize $size = PaperSize::A4) => [ShopService::Print, $size, ColorMode::Color, $price];
        $copy = fn (int $price) => [ShopService::Photocopy, PaperSize::A4, ColorMode::BlackWhite, $price];
        $item = fn (ShopService $service, int $price) => [$service, null, null, $price];

        return [
            ['slug' => 'contoh-print-a', 'name' => 'Contoh Print A', 'latitude' => -7.2875310, 'longitude' => 112.6370120,
                'hours' => $this->hours('08:00', '21:00', sunday: false),
                'prices' => [$bw(500), $color(1500), $copy(300), $item(ShopService::SpiralBinding, 5000)]],
            ['slug' => 'contoh-print-b', 'name' => 'Contoh Print B', 'latitude' => -7.2862000, 'longitude' => 112.6402000,
                'hours' => $this->hours('08:00', '21:00', sunday: false),
                'prices' => [$bw(700), $color(2000)]],
            ['slug' => 'contoh-copy-center', 'name' => 'Contoh Copy Center', 'latitude' => -7.2841000, 'longitude' => 112.6335000,
                'hours' => $this->hours('07:00', '22:00'),
                'prices' => [$bw(400), $color(1200), $item(ShopService::SpiralBinding, 4000), $item(ShopService::TapeBinding, 3000), $item(ShopService::Laminating, 3000)]],
            ['slug' => 'contoh-print-kampus', 'name' => 'Contoh Print Kampus', 'latitude' => -7.2853000, 'longitude' => 112.6318000,
                'hours' => $this->hours('07:30', '18:00', saturday: false, sunday: false),
                'prices' => [$bw(350), $bw(400, PaperSize::F4), $color(1000)]],
            ['slug' => 'contoh-digital-printing', 'name' => 'Contoh Digital Printing', 'latitude' => -7.2899000, 'longitude' => 112.6441000,
                'hours' => $this->hours('09:00', '20:00'),
                'prices' => [$bw(600), $color(1500), $color(5000, PaperSize::A3), $item(ShopService::HardcoverBinding, 25000)]],
            ['slug' => 'contoh-print-malam', 'name' => 'Contoh Print Malam', 'latitude' => -7.2826000, 'longitude' => 112.6389000,
                'hours' => $this->hours('16:00', '23:00'),
                'prices' => [$bw(500), $color(1800), $item(ShopService::SpiralBinding, 6000)]],
            ['slug' => 'contoh-fotokopi-hemat', 'name' => 'Contoh Fotokopi Hemat', 'latitude' => -7.2912000, 'longitude' => 112.6352000,
                'hours' => $this->hours('08:00', '17:00', sunday: false),
                'prices' => [$bw(300), $copy(250), $item(ShopService::TapeBinding, 2500)]],
            ['slug' => 'contoh-print-ekspres', 'name' => 'Contoh Print Ekspres', 'latitude' => -7.2878000, 'longitude' => 112.6478000,
                'hours' => $this->hours('06:00', '23:00'),
                'prices' => [$bw(800), $color(2500), $item(ShopService::SpiralBinding, 7000), $item(ShopService::Scan, 1000)]],
        ];
    }

    /**
     * @return array<string, list<array{open: string, close: string}>>
     */
    private function hours(string $open, string $close, bool $saturday = true, bool $sunday = true): array
    {
        $slot = [['open' => $open, 'close' => $close]];

        return [
            'mon' => $slot, 'tue' => $slot, 'wed' => $slot, 'thu' => $slot, 'fri' => $slot,
            'sat' => $saturday ? $slot : [],
            'sun' => $sunday ? $slot : [],
        ];
    }
}
