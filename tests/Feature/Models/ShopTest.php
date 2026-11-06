<?php

use App\Enums\PriceSource;
use App\Enums\ShopService;
use App\Models\PriceList;
use App\Models\Report;
use App\Models\Shop;

test('shop casts services to enums and opening hours to an array', function () {
    $shop = Shop::factory()->create()->fresh();

    expect($shop->services->first())->toBeInstanceOf(ShopService::class)
        ->and($shop->opening_hours['mon'][0])->toBe(['open' => '08:00', 'close' => '21:00'])
        ->and($shop->opening_hours['sun'])->toBe([])
        ->and($shop->latitude)->toBeFloat();
});

test('shop has price lists stored as whole rupiah', function () {
    $shop = Shop::factory()
        ->has(PriceList::factory()->verified()->state(['price' => 500]))
        ->create();

    $price = $shop->priceLists()->sole();

    expect($price->price)->toBe(500)
        ->and($price->source)->toBe(PriceSource::Verified);
});

test('deleting a shop removes its prices and reports', function () {
    $shop = Shop::factory()
        ->has(PriceList::factory()->count(2))
        ->has(Report::factory())
        ->create();

    $shop->delete();

    expect(PriceList::count())->toBe(0)
        ->and(Report::count())->toBe(0);
});
