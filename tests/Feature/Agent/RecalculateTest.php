<?php

use App\Ai\Agents\PrintRequestAgent;
use App\Enums\ColorMode;
use App\Enums\PaperSize;
use App\Enums\PriceUnit;
use App\Enums\ShopService;
use App\Models\PriceList;
use App\Models\Shop;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $shop = Shop::factory()->create(['name' => 'Print Murah', 'whatsapp' => '6281234567890']);

    foreach ([[PaperSize::A4, 500], [PaperSize::F4, 600]] as [$paper, $price]) {
        PriceList::factory()->for($shop)->create(['service' => ShopService::Print, 'paper_size' => $paper, 'color_mode' => ColorMode::BlackWhite, 'price' => $price, 'unit' => PriceUnit::Page]);
    }
});

function editedSpec(array $overrides = []): array
{
    return [
        'paper_size' => 'a4', 'copies' => 1, 'binding' => null,
        'bw_pages' => 10, 'color_pages' => 0, 'bw_runs' => [], 'color_runs' => [], 'assumed' => [],
        ...$overrides,
    ];
}

test('an edited spec is priced again from the price list without calling the AI', function () {
    PrintRequestAgent::fake()->preventStrayPrompts();

    $this->post('/agent/hitung-ulang', editedSpec(['paper_size' => 'f4', 'copies' => 2]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('agent/index')
            ->where('spec.paperSize', 'f4')
            ->where('spec.copies', 2)
            ->where('estimates.0.total', 10 * 600 * 2));

    PrintRequestAgent::assertNeverPrompted();
});

test('recalculating does not use up the daily quota', function () {
    config(['cariprint.ai.quota.guest' => 2]);

    $remaining = fn () => $this->post('/agent/hitung-ulang', editedSpec())->viewData('page')['props']['quota']['remaining'];

    expect($remaining())->toBe(2)->and($remaining())->toBe(2)->and($remaining())->toBe(2);
});

test('the values the user did not edit stay marked as assumed', function () {
    $this->post('/agent/hitung-ulang', editedSpec(['assumed' => ['copies']]))
        ->assertInertia(fn (Assert $page) => $page->where('spec.assumed', ['copies']));
});

test('page positions survive a recalculation and reach the WhatsApp message', function () {
    $this->post('/agent/hitung-ulang', editedSpec([
        'bw_pages' => 4, 'color_pages' => 0, 'bw_runs' => [['from' => 4, 'to' => 6], ['from' => 9, 'to' => 9]],
    ]))->assertInertia(fn (Assert $page) => $page
        ->where('spec.bwRuns', [['from' => 4, 'to' => 6], ['from' => 9, 'to' => 9]])
        ->where('estimates.0.whatsappMessage', fn ($message) => str_contains($message, '(hal. 4-6, 9)')));
});

test('an invalid edited spec is rejected', function (array $overrides, string $field) {
    $this->postJson('/agent/hitung-ulang', editedSpec($overrides))
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$field]);
})->with([
    'unknown paper size' => [['paper_size' => 'a0'], 'paper_size'],
    'zero copies' => [['copies' => 0], 'copies'],
    'unknown binding' => [['binding' => 'staples'], 'binding'],
    'negative pages' => [['bw_pages' => -1], 'bw_pages'],
    'run that ends before it starts' => [['bw_runs' => [['from' => 5, 'to' => 2]]], 'bw_runs.0.to'],
]);
