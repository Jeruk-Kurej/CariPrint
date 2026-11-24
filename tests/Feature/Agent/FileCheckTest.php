<?php

use App\Enums\ColorMode;
use App\Enums\PaperSize;
use App\Enums\PriceUnit;
use App\Enums\ShopService;
use App\Models\PriceList;
use App\Models\Shop;

function priceShop(string $name, int $bw, int $color): Shop
{
    $shop = Shop::factory()->create(['name' => $name]);

    foreach ([[ColorMode::BlackWhite, $bw], [ColorMode::Color, $color]] as [$mode, $price]) {
        PriceList::factory()->create([
            'shop_id' => $shop->id, 'service' => ShopService::Print, 'paper_size' => PaperSize::A4,
            'color_mode' => $mode, 'price' => $price, 'unit' => PriceUnit::Page,
        ]);
    }

    return $shop;
}

test('a guest gets each page judged and a suggestion without any AI call', function () {
    $this->postJson('/agent/cek-warna', ['shares' => [0.0, 0.05, 0.4]])
        ->assertOk()
        ->assertJson(['needs' => ['bw', 'maybe', 'color'], 'suggested' => [3], 'savings' => null]);
});

test('the savings name the cheapest shop for the suggested pages', function () {
    priceShop('Print Mahal', 800, 2500);
    priceShop('Print Murah', 500, 1500);

    $this->postJson('/agent/cek-warna', ['shares' => [0.0, 0.05, 0.4, 0.0]])
        ->assertOk()
        ->assertJsonPath('savings.shop', 'Print Murah')
        ->assertJsonPath('savings.chosenTotal', 3 * 500 + 1500)
        ->assertJsonPath('savings.allColorTotal', 2 * 500 + 2 * 1500)
        ->assertJsonPath('savings.saving', 1000);
});

test('the pages the user picked replace the suggestion in the savings', function () {
    priceShop('Print Murah', 500, 1500);

    $this->postJson('/agent/cek-warna', ['shares' => [0.0, 0.05, 0.4, 0.0], 'chosen' => []])
        ->assertOk()
        ->assertJsonPath('suggested', [3])
        ->assertJsonPath('savings.chosenTotal', 4 * 500)
        ->assertJsonPath('savings.saving', 2000);
});

test('draft shops are not used for the savings', function () {
    $shop = priceShop('Print Draf', 100, 200);
    $shop->update(['status' => 'draft']);

    $this->postJson('/agent/cek-warna', ['shares' => [0.5]])->assertOk()->assertJsonPath('savings', null);
});

test('the request is validated', function (array $payload, string $field) {
    $this->postJson('/agent/cek-warna', $payload)->assertUnprocessable()->assertJsonValidationErrors($field);
})->with([
    'no shares' => [[], 'shares'],
    'empty shares' => [['shares' => []], 'shares'],
    'a share over 1' => [['shares' => [0.2, 1.5]], 'shares.1'],
    'a negative share' => [['shares' => [-0.1]], 'shares.0'],
    'a share that is not a number' => [['shares' => ['abu']], 'shares.0'],
    'too many pages' => [['shares' => array_fill(0, 1001, 0.1)], 'shares'],
    'a chosen page past the end' => [['shares' => [0.2, 0.3], 'chosen' => [3]], 'chosen'],
    'a chosen page of zero' => [['shares' => [0.2], 'chosen' => [0]], 'chosen.0'],
    'the same page twice' => [['shares' => [0.2, 0.3], 'chosen' => [1, 1]], 'chosen.0'],
]);

test('the file check is limited per minute', function () {
    foreach (range(1, 60) as $call) {
        $this->postJson('/agent/cek-warna', ['shares' => [0.5]])->assertOk();
    }

    $this->postJson('/agent/cek-warna', ['shares' => [0.5]])->assertStatus(429);
});
