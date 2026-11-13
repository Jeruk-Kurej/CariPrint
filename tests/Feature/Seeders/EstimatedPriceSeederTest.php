<?php

use App\Enums\PriceSource;
use App\Enums\ShopService;
use App\Models\PriceList;
use App\Models\Shop;
use Database\Seeders\EstimatedPriceSeeder;

test('estimated prices follow the services each shop offers', function () {
    $printOnly = Shop::factory()->create(['services' => [ShopService::Print]]);
    $withCopy = Shop::factory()->create(['services' => [ShopService::Print, ShopService::Photocopy]]);

    $this->seed(EstimatedPriceSeeder::class);

    expect($printOnly->priceLists()->count())->toBe(6)
        ->and($withCopy->priceLists()->count())->toBe(7)
        ->and($printOnly->priceLists()->where('service', ShopService::Photocopy->value)->exists())->toBeFalse()
        ->and(PriceList::where('source', '!=', PriceSource::Estimated->value)->count())->toBe(0);
});

test('shops that already have verified prices are left alone', function () {
    $shop = Shop::factory()->create(['services' => [ShopService::Print]]);
    PriceList::factory()->for($shop)->verified()->create();

    $this->seed(EstimatedPriceSeeder::class);

    expect($shop->priceLists()->count())->toBe(1)
        ->and($shop->priceLists()->first()->source)->toBe(PriceSource::Verified);
});

test('seeding twice gives the same prices without duplicates', function () {
    $shop = Shop::factory()->create(['services' => [ShopService::Print]]);

    $this->seed(EstimatedPriceSeeder::class);
    $first = $shop->priceLists()->orderBy('id')->pluck('price')->all();
    $this->seed(EstimatedPriceSeeder::class);

    expect($shop->priceLists()->count())->toBe(6)
        ->and($shop->priceLists()->orderBy('id')->pluck('price')->all())->toBe($first);
});

test('prices are whole Rupiah within 80 to 120 percent of the base price', function () {
    Shop::factory()->create(['services' => [ShopService::Print]]);

    $this->seed(EstimatedPriceSeeder::class);

    $a4Black = PriceList::where('paper_size', 'a4')->where('color_mode', 'bw')->firstOrFail();

    expect($a4Black->price)->toBeBetween(400, 600)->and($a4Black->price % 50)->toBe(0);
});
