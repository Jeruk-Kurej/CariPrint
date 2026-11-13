<?php

use App\Ai\Agents\PrintRequestAgent;
use App\Enums\ColorMode;
use App\Enums\PaperSize;
use App\Enums\PriceSource;
use App\Enums\PriceUnit;
use App\Enums\ShopService;
use App\Models\PriceList;
use App\Models\Shop;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $cheap = Shop::factory()->create(['name' => 'Print Murah']);
    PriceList::factory()->for($cheap)->create(['service' => ShopService::Print, 'paper_size' => PaperSize::A4, 'color_mode' => ColorMode::BlackWhite, 'price' => 300, 'unit' => PriceUnit::Page, 'source' => PriceSource::Verified]);

    $pricey = Shop::factory()->create(['name' => 'Print Mahal']);
    PriceList::factory()->for($pricey)->create(['service' => ShopService::Print, 'paper_size' => PaperSize::A4, 'color_mode' => ColorMode::BlackWhite, 'price' => 700, 'unit' => PriceUnit::Page]);
});

test('AI turns the request into a spec and the price comes from the database', function () {
    PrintRequestAgent::fake([[
        'paper_size' => 'a4', 'bw_pages' => 10, 'color_pages' => 0, 'copies' => 1, 'binding' => null,
    ]]);

    $this->post('/agent', ['request' => 'Print file ini halaman 1-10 saja, hitam putih, paling murah.'])
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('agent/index')
            ->where('spec.bwPages', 10)
            ->where('estimates.0.name', 'Print Murah')
            ->where('estimates.0.total', 3000)
            ->where('estimates.1.total', 7000));

    PrintRequestAgent::assertPrompted(fn ($prompt) => $prompt->contains('hitam putih'));
});

test('a shop without the needed service is listed as unavailable, not free', function () {
    PrintRequestAgent::fake([[
        'paper_size' => 'a4', 'bw_pages' => 0, 'color_pages' => 0, 'copies' => 1, 'binding' => 'spiral_binding',
    ]]);

    $this->post('/agent', ['request' => 'jilid spiral'])
        ->assertInertia(fn (Assert $page) => $page
            ->where('estimates.0.total', null)
            ->where('estimates.0.missing', ['spiral_binding']));
});

test('empty request is rejected before calling the AI', function () {
    PrintRequestAgent::fake()->preventStrayPrompts();

    $this->postJson('/agent', ['request' => ''])->assertUnprocessable();

    PrintRequestAgent::assertNeverPrompted();
});

test('a failing AI call shows a friendly error instead of crashing', function () {
    PrintRequestAgent::fake(fn () => throw new RuntimeException('provider down'));

    $this->from('/agent')
        ->post('/agent', ['request' => 'print 5 halaman'])
        ->assertRedirect('/agent')
        ->assertSessionHasErrors('request');
});

test('the estimate page warns when any price is sample data', function () {
    PriceList::factory()->for(Shop::where('name', 'Print Murah')->firstOrFail())->demo()->create(['service' => ShopService::Photocopy]);
    PrintRequestAgent::fake([[
        'paper_size' => 'a4', 'bw_pages' => 1, 'color_pages' => 0, 'copies' => 1, 'binding' => null,
    ]]);

    $this->post('/agent', ['request' => 'print 1 halaman'])
        ->assertInertia(fn (Assert $page) => $page->where('hasDemoPrices', true));
});
