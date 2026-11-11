<?php

use App\Models\Shop;
use Database\Seeders\ShopSeeder;

test('sample shops are seeded with prices inside the service area', function () {
    $this->seed(ShopSeeder::class);

    $area = config('cariprint.area');
    $shops = Shop::with('priceLists')->get();

    expect($shops)->toHaveCount(8);

    $shops->each(function (Shop $shop) use ($area) {
        expect($shop->name)->toStartWith('Contoh')
            ->and($shop->priceLists)->not->toBeEmpty()
            ->and($shop->latitude)->toBeBetween($area['min_latitude'], $area['max_latitude'])
            ->and($shop->longitude)->toBeBetween($area['min_longitude'], $area['max_longitude']);
    });
});

test('seeding twice does not duplicate shops or prices', function () {
    $this->seed(ShopSeeder::class);
    $priceCount = Shop::where('slug', 'contoh-print-a')->firstOrFail()->priceLists()->count();

    $this->seed(ShopSeeder::class);

    expect(Shop::count())->toBe(8)
        ->and(Shop::where('slug', 'contoh-print-a')->firstOrFail()->priceLists()->count())->toBe($priceCount);
});
