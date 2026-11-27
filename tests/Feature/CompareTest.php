<?php

use App\Enums\ColorMode;
use App\Enums\PaperSize;
use App\Enums\PriceUnit;
use App\Enums\ShopService;
use App\Models\PriceList;
use App\Models\Shop;
use Inertia\Testing\AssertableInertia as Assert;

test('compare page lines up prices and flags the cheapest shop', function () {
    $print = ['service' => ShopService::Print, 'paper_size' => PaperSize::A4, 'color_mode' => ColorMode::BlackWhite, 'unit' => PriceUnit::Page];

    $alfa = Shop::factory()->create(['name' => 'Alfa Print']);
    PriceList::factory()->for($alfa)->create([...$print, 'price' => 500]);
    $beta = Shop::factory()->create(['name' => 'Beta Print']);
    PriceList::factory()->for($beta)->create([...$print, 'price' => 300]);

    $this->get('/bandingkan')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('compare/index')
            ->has('shops', 2)
            ->where('rows.0.prices', [500, 300])
            ->where('rows.0.cheapest', [1]));
});

test('compare page works with no shops', function () {
    $this->get('/bandingkan')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->has('shops', 0)->has('rows', 0));
});

test('information pages are public', function (string $path, string $component) {
    $this->get($path)->assertOk()->assertInertia(fn (Assert $page) => $page->component($component));
})->with([
    ['/cara-kerja', 'info/how-it-works'],
    ['/tentang', 'info/about'],
    ['/pertanyaan', 'info/faq'],
]);

test('compare page lists only shops that have prices and says how many do not', function () {
    PriceList::factory()->for(Shop::factory()->create(['name' => 'Ada Harga', 'address' => 'Jl. Ngagel 1']))->create();
    Shop::factory()->count(2)->create();
    Shop::factory()->draft()->create();

    $this->get('/bandingkan')->assertInertia(fn (Assert $page) => $page
        ->has('shops', 1)
        ->where('shops.0.name', 'Ada Harga')
        ->where('shops.0.address', 'Jl. Ngagel 1')
        ->where('withoutPrices', 2));
});
