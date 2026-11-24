<?php

use App\Enums\ColorMode;
use App\Enums\PaperSize;
use App\Enums\PriceUnit;
use App\Enums\ShopService;
use App\Services\Pricing\ColorSavings;
use App\Services\Pricing\PriceCalculator;

/** A shop with an A4 black and white price and, unless left out, an A4 colour price. */
function printShop(string $name, int $bwPrice, ?int $colorPrice)
{
    $prices = [['service' => ShopService::Print, 'paper_size' => PaperSize::A4, 'color_mode' => ColorMode::BlackWhite, 'price' => $bwPrice, 'unit' => PriceUnit::Page]];

    if ($colorPrice !== null) {
        $prices[] = ['service' => ShopService::Print, 'paper_size' => PaperSize::A4, 'color_mode' => ColorMode::Color, 'price' => $colorPrice, 'unit' => PriceUnit::Page];
    }

    $shop = shopWithPrices($prices);
    $shop->name = $name;
    $shop->slug = str($name)->slug()->toString();

    return $shop;
}

function savings(): ColorSavings
{
    return new ColorSavings(new PriceCalculator);
}

test('the saving is the colour price of the pages the user moves to black and white', function () {
    $result = savings()->fromShops(collect([printShop('Print A', 500, 1500)]), totalPages: 10, chosenColorPages: [1, 2], coloredPages: [1, 2, 3, 4, 5]);

    expect($result)->toBe([
        'shop' => 'Print A',
        'slug' => 'print-a',
        'chosenTotal' => 8 * 500 + 2 * 1500,
        'allColorTotal' => 5 * 500 + 5 * 1500,
        'allBlackWhiteTotal' => 10 * 500,
        'saving' => 3000,
    ]);
});

test('the cheapest shop for the user choice is named', function () {
    $result = savings()->fromShops(
        collect([printShop('Mahal', 800, 2500), printShop('Murah', 400, 1200)]),
        totalPages: 10,
        chosenColorPages: [1],
        coloredPages: [1, 2, 3],
    );

    expect($result['shop'])->toBe('Murah')
        ->and($result['chosenTotal'])->toBe(9 * 400 + 1200);
});

test('a shop without a colour price is skipped', function () {
    $result = savings()->fromShops(
        collect([printShop('Tanpa Warna', 300, null), printShop('Lengkap', 500, 1500)]),
        totalPages: 4,
        chosenColorPages: [1],
        coloredPages: [1, 2],
    );

    expect($result['shop'])->toBe('Lengkap');
});

test('no saving is reported when the file has no colour or no shop prices both modes', function () {
    expect(savings()->fromShops(collect([printShop('Print A', 500, 1500)]), 5, [], []))->toBeNull()
        ->and(savings()->fromShops(collect([printShop('Tanpa Warna', 300, null)]), 5, [1], [1]))->toBeNull()
        ->and(savings()->fromShops(collect(), 5, [1], [1]))->toBeNull();
});

test('keeping every coloured page in colour saves nothing', function () {
    $result = savings()->fromShops(collect([printShop('Print A', 500, 1500)]), 10, [1, 2, 3], [1, 2, 3]);

    expect($result['saving'])->toBe(0);
});

test('choosing colour for a page that had none costs extra', function () {
    $result = savings()->fromShops(collect([printShop('Print A', 500, 1500)]), 10, [1, 2, 3], [1, 2]);

    expect($result['saving'])->toBe(-1000);
});
