<?php

use App\Enums\ColorMode;
use App\Enums\PaperSize;
use App\Enums\PriceSource;
use App\Enums\ShopService;
use App\Models\PriceList;
use App\Models\Shop;
use App\Services\Pricing\PrintPrices;
use Illuminate\Database\Eloquent\Collection;

function shopForChoices(array ...$prices): Shop
{
    $defaults = ['service' => ShopService::Print, 'paper_size' => PaperSize::A4, 'color_mode' => ColorMode::BlackWhite, 'source' => PriceSource::Estimated];

    return (new Shop)->setRelation('priceLists', new Collection(
        array_map(fn (array $price) => new PriceList([...$defaults, ...$price]), $prices),
    ));
}

test('each paper and colour choice gets its own cheapest print price', function () {
    $choices = (new PrintPrices)->byChoice(shopForChoices(
        ['price' => 500],
        ['price' => 1500, 'color_mode' => ColorMode::Color],
        ['price' => 700, 'paper_size' => PaperSize::F4],
    ));

    expect(array_map(fn (array $choice) => $choice['price'], $choices))->toEqualCanonicalizing([
        'any_any' => 500,
        'any_bw' => 500,
        'any_color' => 1500,
        'a4_any' => 500,
        'a4_bw' => 500,
        'a4_color' => 1500,
        'f4_any' => 700,
        'f4_bw' => 700,
    ])->not->toHaveKey('f4_color');
});

test('a verified price wins over an estimate of the same amount', function () {
    $choices = (new PrintPrices)->byChoice(shopForChoices(
        ['price' => 500],
        ['price' => 500, 'source' => PriceSource::Verified],
    ));

    expect($choices['a4_bw']['source'])->toBe('verified');
});

test('other services never count as a print price', function () {
    expect((new PrintPrices)->byChoice(shopForChoices(['price' => 100, 'service' => ShopService::Photocopy])))->toBe([]);
});

test('a print price without paper size or colour only matches the open choice', function () {
    $choices = (new PrintPrices)->byChoice(shopForChoices(['price' => 400, 'paper_size' => null, 'color_mode' => null]));

    expect(array_keys($choices))->toBe(['any_any']);
});
