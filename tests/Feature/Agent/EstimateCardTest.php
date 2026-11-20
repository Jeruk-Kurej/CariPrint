<?php

use App\Ai\Agents\PrintRequestAgent;
use App\Enums\ShopService;
use App\Models\PriceList;
use App\Models\Shop;
use App\Services\Location\DistanceService;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    PrintRequestAgent::fake([[
        'paper_size' => 'a4', 'page_ranges' => [], 'bw_pages' => 10, 'color_pages' => 0, 'copies' => 1, 'binding' => null,
    ]]);
});

test('every shop that shares the lowest total is flagged as cheapest', function () {
    foreach (['Alfa Print' => 500, 'Beta Print' => 500, 'Gama Print' => 700] as $name => $price) {
        PriceList::factory()->for(Shop::factory()->create(['name' => $name]))->create(['price' => $price]);
    }

    $this->post('/agent', ['request' => 'print 10 halaman'])
        ->assertInertia(fn (Assert $page) => $page
            ->where('estimates.0.cheapest', true)
            ->where('estimates.1.cheapest', true)
            ->where('estimates.2.cheapest', false));
});

test('a shop that cannot price the request is never flagged as cheapest', function () {
    PriceList::factory()->for(Shop::factory()->create())->create(['service' => ShopService::Photocopy]);

    $this->post('/agent', ['request' => 'print 10 halaman'])
        ->assertInertia(fn (Assert $page) => $page
            ->where('estimates.0.total', null)
            ->where('estimates.0.cheapest', false));
});

test('distance on the card is the straight-line distance from the coordinates sent', function () {
    $shop = Shop::factory()->create();
    PriceList::factory()->for($shop)->create();
    $expected = round((new DistanceService)->kilometers(-7.2870, 112.6375, $shop->latitude, $shop->longitude), 2);

    $this->post('/agent', ['request' => 'print 10 halaman', 'latitude' => -7.2870, 'longitude' => 112.6375])
        ->assertInertia(fn (Assert $page) => $page->where('estimates.0.distanceKm', $expected));
});

test('cards have no distance when the browser sends no location', function () {
    PriceList::factory()->for(Shop::factory()->create())->create();

    $this->post('/agent', ['request' => 'print 10 halaman'])
        ->assertInertia(fn (Assert $page) => $page->where('estimates.0.distanceKm', null));
});

test('shops without any price list are left out of the estimates and counted instead', function () {
    PriceList::factory()->for(Shop::factory()->create(['name' => 'Ada Harga']))->create(['price' => 500]);
    Shop::factory()->count(3)->create();

    $this->post('/agent', ['request' => 'print 10 halaman'])
        ->assertInertia(fn (Assert $page) => $page
            ->has('estimates', 1)
            ->where('estimates.0.name', 'Ada Harga')
            ->where('shopsWithoutPrices', 3));
});
