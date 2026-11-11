<?php

use App\Enums\ColorMode;
use App\Enums\PaperSize;
use App\Enums\PriceUnit;
use App\Enums\ShopService;
use App\Models\PriceList;
use App\Models\Shop;
use App\Services\Pricing\PriceComparison;

/** Builds shops with prices in memory only, so the comparison is tested without a database. */
function comparedShop(int $id, string $name, array $prices): Shop
{
    $shop = Shop::make(['name' => $name]);
    $shop->id = $id;
    $shop->slug = strtolower($name);
    $shop->setRelation('priceLists', collect(array_map(fn (array $price) => PriceList::make($price), $prices)));

    return $shop;
}

function blackWhiteA4(int $price): array
{
    return ['service' => ShopService::Print, 'paper_size' => PaperSize::A4, 'color_mode' => ColorMode::BlackWhite, 'price' => $price, 'unit' => PriceUnit::Page];
}

test('the same service is placed on one row with a price per shop', function () {
    $result = (new PriceComparison)->build(collect([
        comparedShop(1, 'Alfa', [blackWhiteA4(500)]),
        comparedShop(2, 'Beta', [blackWhiteA4(300)]),
    ]));

    expect($result['rows'])->toHaveCount(1)
        ->and($result['rows'][0]['prices'])->toBe([500, 300])
        ->and($result['shops'][1]['name'])->toBe('Beta');
});

test('the cheapest shop on a row is flagged', function () {
    $result = (new PriceComparison)->build(collect([
        comparedShop(1, 'Alfa', [blackWhiteA4(500)]),
        comparedShop(2, 'Beta', [blackWhiteA4(300)]),
        comparedShop(3, 'Gamma', [blackWhiteA4(700)]),
    ]));

    expect($result['rows'][0]['cheapest'])->toBe([1]);
});

test('a tie flags every shop at the lowest price', function () {
    $result = (new PriceComparison)->build(collect([
        comparedShop(1, 'Alfa', [blackWhiteA4(300)]),
        comparedShop(2, 'Beta', [blackWhiteA4(300)]),
    ]));

    expect($result['rows'][0]['cheapest'])->toBe([0, 1]);
});

test('a shop without the service gets a null and a lone price is not called cheapest', function () {
    $result = (new PriceComparison)->build(collect([
        comparedShop(1, 'Alfa', [blackWhiteA4(500)]),
        comparedShop(2, 'Beta', []),
    ]));

    expect($result['rows'][0]['prices'])->toBe([500, null])
        ->and($result['rows'][0]['cheapest'])->toBe([]);
});

test('rows follow the service order and keep paper sizes apart', function () {
    $laminating = ['service' => ShopService::Laminating, 'paper_size' => null, 'color_mode' => null, 'price' => 3000, 'unit' => PriceUnit::Item];
    $a3 = ['service' => ShopService::Print, 'paper_size' => PaperSize::A3, 'color_mode' => ColorMode::Color, 'price' => 5000, 'unit' => PriceUnit::Page];

    $result = (new PriceComparison)->build(collect([
        comparedShop(1, 'Alfa', [$laminating, $a3, blackWhiteA4(500)]),
    ]));

    expect(array_column($result['rows'], 'service'))->toBe(['Print', 'Print', 'Laminating'])
        ->and($result['rows'][0]['paperSize'])->toBe('a4')
        ->and($result['rows'][1]['paperSize'])->toBe('a3');
});
