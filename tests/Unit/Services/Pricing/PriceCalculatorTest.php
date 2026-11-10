<?php

use App\Enums\ColorMode;
use App\Enums\PaperSize;
use App\Enums\PriceUnit;
use App\Enums\ShopService;
use App\Models\PriceList;
use App\Models\Shop;
use App\Services\Pricing\PriceCalculator;
use App\Services\Pricing\PrintSpec;

/** Builds the shop and its prices in memory only, so the calculator is tested without a database. */
function shopWithPrices(array $prices): Shop
{
    $shop = Shop::make();
    $shop->setRelation('priceLists', collect(array_map(fn (array $price) => PriceList::make($price), $prices)));

    return $shop;
}

test('total is the sum of black and white and colour pages', function () {
    $shop = shopWithPrices([
        ['service' => ShopService::Print, 'paper_size' => PaperSize::A4, 'color_mode' => ColorMode::BlackWhite, 'price' => 500, 'unit' => PriceUnit::Page],
        ['service' => ShopService::Print, 'paper_size' => PaperSize::A4, 'color_mode' => ColorMode::Color, 'price' => 1500, 'unit' => PriceUnit::Page],
    ]);

    $breakdown = (new PriceCalculator)->forShop($shop, new PrintSpec(PaperSize::A4, bwPages: 8, colorPages: 2));

    expect($breakdown->total())->toBe(8 * 500 + 2 * 1500)
        ->and($breakdown->lines)->toHaveCount(2);
});

test('copies multiply the page cost', function () {
    $shop = shopWithPrices([
        ['service' => ShopService::Print, 'paper_size' => PaperSize::A4, 'color_mode' => ColorMode::BlackWhite, 'price' => 500, 'unit' => PriceUnit::Page],
    ]);

    $breakdown = (new PriceCalculator)->forShop($shop, new PrintSpec(PaperSize::A4, bwPages: 10, colorPages: 0, copies: 3));

    expect($breakdown->total())->toBe(10 * 3 * 500);
});

test('binding is charged once per copy by item', function () {
    $shop = shopWithPrices([
        ['service' => ShopService::Print, 'paper_size' => PaperSize::A4, 'color_mode' => ColorMode::BlackWhite, 'price' => 500, 'unit' => PriceUnit::Page],
        ['service' => ShopService::SpiralBinding, 'paper_size' => null, 'color_mode' => null, 'price' => 7000, 'unit' => PriceUnit::Item],
    ]);

    $breakdown = (new PriceCalculator)->forShop($shop, new PrintSpec(PaperSize::A4, bwPages: 4, colorPages: 0, copies: 2, binding: ShopService::SpiralBinding));

    expect($breakdown->total())->toBe(4 * 2 * 500 + 2 * 7000);
});

test('a shop without the needed service is unavailable, not free', function () {
    $shop = shopWithPrices([
        ['service' => ShopService::Print, 'paper_size' => PaperSize::A4, 'color_mode' => ColorMode::BlackWhite, 'price' => 500, 'unit' => PriceUnit::Page],
    ]);

    $breakdown = (new PriceCalculator)->forShop($shop, new PrintSpec(PaperSize::A4, bwPages: 4, colorPages: 2));

    expect($breakdown->isAvailable())->toBeFalse()
        ->and($breakdown->total())->toBeNull()
        ->and($breakdown->missing)->toBe(['print-color']);
});

test('a spec with no pages costs nothing and is available', function () {
    $shop = shopWithPrices([]);

    $breakdown = (new PriceCalculator)->forShop($shop, new PrintSpec(PaperSize::A4, bwPages: 0, colorPages: 0));

    expect($breakdown->total())->toBe(0)
        ->and($breakdown->lines)->toBe([]);
});

test('totals are whole rupiah', function () {
    $shop = shopWithPrices([
        ['service' => ShopService::Print, 'paper_size' => PaperSize::A4, 'color_mode' => ColorMode::BlackWhite, 'price' => 333, 'unit' => PriceUnit::Page],
    ]);

    expect((new PriceCalculator)->forShop($shop, new PrintSpec(PaperSize::A4, bwPages: 3, colorPages: 0))->total())
        ->toBeInt()->toBe(999);
});
