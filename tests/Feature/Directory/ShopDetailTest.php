<?php

use App\Enums\PriceSource;
use App\Enums\ShopService;
use App\Models\PriceList;
use App\Models\Shop;
use Inertia\Testing\AssertableInertia as Assert;

test('shop page shows hours, services and prices with their source', function () {
    $shop = Shop::factory()
        ->has(PriceList::factory()->verified()->state(['price' => 500]))
        ->has(PriceList::factory()->state(['price' => 1500, 'source' => PriceSource::Estimated]))
        ->create(['name' => 'Print Sejahtera', 'slug' => 'print-sejahtera', 'services' => [ShopService::Print]]);

    $this->get(route('directory.show', $shop))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('directory/show')
            ->where('shop.name', 'Print Sejahtera')
            ->where('shop.services', ['Print'])
            ->where('shop.latitude', $shop->latitude)
            ->where('shop.longitude', $shop->longitude)
            ->has('shop.prices', 2)
            ->where('shop.prices.0.source', 'verified')
            ->where('shop.prices.1.source', 'estimated')
            ->has('shop.openingHours.mon'));
});

test('shop page uses the slug in the URL', function () {
    $shop = Shop::factory()->create(['slug' => 'contoh-print']);

    $this->get('/shops/contoh-print')->assertOk();
    expect(route('directory.show', $shop))->toEndWith('/shops/contoh-print');
});

test('unknown shop returns not found', function () {
    $this->get('/shops/tidak-ada')->assertNotFound();
});

test('starting price counts print prices only, not cheaper services', function () {
    $shop = Shop::factory()
        ->has(PriceList::factory()->state(['service' => ShopService::Print, 'price' => 500]))
        ->has(PriceList::factory()->state(['service' => ShopService::Scan, 'price' => 100]))
        ->create(['slug' => 'harga-print']);

    $this->get(route('directory.show', $shop))
        ->assertInertia(fn (Assert $page) => $page->where('shop.startingPrice', 500));

    $this->get('/tempat')
        ->assertInertia(fn (Assert $page) => $page->where('shops.0.startingPrice', 500));
});

test('a shop without print prices has no starting price', function () {
    $shop = Shop::factory()
        ->has(PriceList::factory()->state(['service' => ShopService::Scan, 'price' => 100]))
        ->create(['slug' => 'hanya-scan']);

    $this->get(route('directory.show', $shop))
        ->assertInertia(fn (Assert $page) => $page->where('shop.startingPrice', null));
});
