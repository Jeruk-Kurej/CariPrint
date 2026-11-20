<?php

use App\Ai\Agents\PrintRequestAgent;
use App\Enums\ColorMode;
use App\Enums\PaperSize;
use App\Enums\ShopService;
use App\Models\PriceList;
use App\Models\Shop;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->live = Shop::factory()->create(['name' => 'Print Tayang']);
    $this->draft = Shop::factory()->draft()->create(['name' => 'Print Draf']);

    foreach ([$this->live, $this->draft] as $shop) {
        PriceList::factory()->for($shop)->create([
            'service' => ShopService::Print, 'paper_size' => PaperSize::A4, 'color_mode' => ColorMode::BlackWhite, 'price' => 500,
        ]);
    }
});

test('the directory lists only published shops', function () {
    $this->get('/')->assertInertia(fn (Assert $page) => $page
        ->has('shops', 1)
        ->where('shops.0.name', 'Print Tayang'));
});

test('searching by location does not reveal drafts', function () {
    $this->post('/shops/nearby', ['latitude' => -7.285, 'longitude' => 112.64])
        ->assertInertia(fn (Assert $page) => $page->has('shops', 1)->where('shops.0.name', 'Print Tayang'));
});

test('a draft has no detail page but a published shop does', function () {
    $this->get("/shops/{$this->draft->slug}")->assertNotFound();
    $this->get("/shops/{$this->live->slug}")->assertOk();
});

test('the price comparison leaves drafts out', function () {
    $this->get('/bandingkan')->assertInertia(fn (Assert $page) => $page->has('shops', 1));
});

test('the print assistant does not estimate costs for drafts', function () {
    PrintRequestAgent::fake([[
        'paper_size' => 'a4', 'bw_pages' => 10, 'color_pages' => 0, 'copies' => 1, 'binding' => null,
    ]]);

    $this->post('/agent', ['request' => 'print 10 halaman hitam putih'])
        ->assertInertia(fn (Assert $page) => $page
            ->has('estimates', 1)
            ->where('estimates.0.name', 'Print Tayang'));
});
