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

test('optional fields the AI leaves out are read as not asked for', function () {
    PrintRequestAgent::fake([[
        'paper_size' => 'a4', 'page_ranges' => [], 'bw_pages' => 5, 'color_pages' => 0, 'copies' => 1,
    ]]);

    $this->post('/agent', ['request' => 'print 5 halaman'])
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('spec.binding', null)
            ->where('estimates.0.total', 1500));
});

test('pages are counted from the length of the attached document, which the AI never sees', function () {
    PrintRequestAgent::fake([[
        'paper_size' => 'a4',
        'page_ranges' => [['from' => 1, 'to' => -1, 'mode' => 'bw'], ['from' => -1, 'to' => -1, 'mode' => 'skip']],
        'bw_pages' => 0, 'color_pages' => 0, 'total_pages' => null, 'copies' => 1, 'binding' => null,
    ]]);

    $this->post('/agent', ['request' => 'Print semua kecuali halaman terakhir', 'total_pages' => 24])
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('spec.bwPages', 23)
            ->where('spec.bwRuns', [['from' => 1, 'to' => 23]])
            ->where('spec.colorRuns', [])
            ->where('estimates.0.total', 6900));

    PrintRequestAgent::assertPrompted(fn ($prompt) => $prompt->contains('A PDF document is attached') && ! $prompt->contains('24'));
});

test('the document length written in the request is used when no file is attached', function () {
    PrintRequestAgent::fake([[
        'paper_size' => 'a4',
        'page_ranges' => [['from' => -3, 'to' => -1, 'mode' => 'bw']],
        'bw_pages' => 0, 'color_pages' => 0, 'total_pages' => 24, 'copies' => 1, 'binding' => null,
    ]]);

    $this->post('/agent', ['request' => 'Dokumen saya tebal, print 3 halaman terakhir'])
        ->assertInertia(fn (Assert $page) => $page
            ->where('spec.bwPages', 3)
            ->where('spec.bwRuns', [['from' => 22, 'to' => 24]]));

    PrintRequestAgent::assertPrompted(fn ($prompt) => $prompt->contains('No document is attached'));
});

test('the attached document decides the length when the request states a different one', function () {
    PrintRequestAgent::fake([[
        'paper_size' => 'a4',
        'page_ranges' => [['from' => 1, 'to' => -1, 'mode' => 'bw']],
        'bw_pages' => 0, 'color_pages' => 0, 'total_pages' => 30, 'copies' => 1, 'binding' => null,
    ]]);

    $this->post('/agent', ['request' => 'Print file ini', 'total_pages' => 24])
        ->assertInertia(fn (Assert $page) => $page->where('spec.bwPages', 24));
});

test('page ranges replace the plain page counts instead of adding to them', function () {
    PrintRequestAgent::fake([[
        'paper_size' => 'a4',
        'page_ranges' => [['from' => 1, 'to' => 10, 'mode' => 'bw']],
        'bw_pages' => 10, 'color_pages' => 0, 'total_pages' => null, 'copies' => 1, 'binding' => null,
    ]]);

    $this->post('/agent', ['request' => 'Print halaman 1-10 saja'])
        ->assertInertia(fn (Assert $page) => $page
            ->where('spec.bwPages', 10)
            ->where('estimates.0.total', 3000));
});

test('pointing at pages from the end without a known length asks for the file or the page count', function () {
    PrintRequestAgent::fake([[
        'paper_size' => 'a4',
        'page_ranges' => [['from' => 1, 'to' => -1, 'mode' => 'bw'], ['from' => -1, 'to' => -1, 'mode' => 'skip']],
        'bw_pages' => 0, 'color_pages' => 0, 'total_pages' => null, 'copies' => 1, 'binding' => null,
    ]]);

    $this->from('/agent')
        ->post('/agent', ['request' => 'Print semua kecuali halaman terakhir'])
        ->assertRedirect('/agent')
        ->assertSessionHasErrors([
            'request' => 'Asisten perlu tahu jumlah halaman dokumenmu. Lampirkan file PDF-nya atau tulis jumlah halamannya.',
        ]);
});

test('a page number beyond the attached document is refused', function () {
    PrintRequestAgent::fake([[
        'paper_size' => 'a4',
        'page_ranges' => [['from' => 1, 'to' => 50, 'mode' => 'bw']],
        'bw_pages' => 0, 'color_pages' => 0, 'total_pages' => null, 'copies' => 1, 'binding' => null,
    ]]);

    $this->from('/agent')
        ->post('/agent', ['request' => 'Print halaman 1-50', 'total_pages' => 30])
        ->assertRedirect('/agent')
        ->assertSessionHasErrors([
            'request' => 'Dokumenmu 30 halaman, tetapi permintaanmu menyebut halaman di luar itu. Periksa lagi nomor halamannya.',
        ]);
});

test('an attachment page count that is not a whole number from 1 is rejected before calling the AI', function (mixed $totalPages) {
    PrintRequestAgent::fake()->preventStrayPrompts();

    $this->postJson('/agent', ['request' => 'Print file ini', 'total_pages' => $totalPages])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['total_pages']);

    PrintRequestAgent::assertNeverPrompted();
})->with([
    'zero' => [0],
    'a negative number' => [-4],
    'text' => ['banyak'],
]);

test('an attachment with more pages than the limit is rejected with a message the user can read', function () {
    config(['cariprint.uploads.max_pages' => 200]);
    PrintRequestAgent::fake()->preventStrayPrompts();

    $this->postJson('/agent', ['request' => 'Print file ini', 'total_pages' => 201])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['total_pages' => 'File PDF maksimal 200 halaman.']);

    PrintRequestAgent::assertNeverPrompted();
});

test('the agent page tells the browser the attachment limits', function () {
    config(['cariprint.uploads.max_megabytes' => 10, 'cariprint.uploads.max_pages' => 200]);

    $this->get('/agent')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('agent/index')
            ->where('fileLimits', ['maxMegabytes' => 10, 'maxPages' => 200]));
});

test('colour only where needed uses the pages the browser found colour on', function () {
    $cheap = Shop::where('name', 'Print Murah')->firstOrFail();
    PriceList::factory()->for($cheap)->create(['service' => ShopService::Print, 'paper_size' => PaperSize::A4, 'color_mode' => ColorMode::Color, 'price' => 1000, 'unit' => PriceUnit::Page]);
    PrintRequestAgent::fake([[
        'paper_size' => 'a4',
        'page_ranges' => [['from' => 1, 'to' => -1, 'mode' => 'auto']],
        'bw_pages' => 0, 'color_pages' => 0, 'total_pages' => null, 'copies' => 1, 'binding' => null,
    ]]);

    $this->post('/agent', ['request' => 'Print semua hitam putih, warna hanya yang perlu', 'total_pages' => 10, 'colored_pages' => [2, 3, 9]])
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('spec.bwPages', 7)
            ->where('spec.colorPages', 3)
            ->where('spec.colorRuns', [['from' => 2, 'to' => 3], ['from' => 9, 'to' => 9]])
            ->where('estimates.0.name', 'Print Murah')
            ->where('estimates.0.total', 5100));

    PrintRequestAgent::assertPrompted(fn ($prompt) => $prompt->contains('The colour of every page has been checked') && ! $prompt->contains('[2, 3, 9]'));
});

test('a file without any colour is printed entirely in black and white', function () {
    PrintRequestAgent::fake([[
        'paper_size' => 'a4',
        'page_ranges' => [['from' => 1, 'to' => -1, 'mode' => 'auto']],
        'bw_pages' => 0, 'color_pages' => 0, 'total_pages' => null, 'copies' => 1, 'binding' => null,
    ]]);

    $this->post('/agent', ['request' => 'Print semua, warna hanya yang perlu', 'total_pages' => 4, 'colored_pages' => []])
        ->assertInertia(fn (Assert $page) => $page
            ->where('spec.bwPages', 4)
            ->where('spec.colorPages', 0));
});

test('asking for colour only where needed without a checked file asks the user to attach one', function () {
    PrintRequestAgent::fake([[
        'paper_size' => 'a4',
        'page_ranges' => [['from' => 1, 'to' => 10, 'mode' => 'auto']],
        'bw_pages' => 0, 'color_pages' => 0, 'total_pages' => null, 'copies' => 1, 'binding' => null,
    ]]);

    $this->from('/agent')
        ->post('/agent', ['request' => 'Print halaman 1-10, warna hanya yang perlu'])
        ->assertRedirect('/agent')
        ->assertSessionHasErrors([
            'request' => 'Asisten belum memeriksa warna halaman. Lampirkan file PDF-nya dulu, atau tulis halaman mana saja yang berwarna.',
        ]);
});

test('a colour list that does not fit the attached file is rejected before calling the AI', function (array $payload) {
    PrintRequestAgent::fake()->preventStrayPrompts();

    $this->postJson('/agent', ['request' => 'Print file ini', ...$payload])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['colored_pages']);

    PrintRequestAgent::assertNeverPrompted();
})->with([
    'a page past the end of the file' => [['total_pages' => 10, 'colored_pages' => [3, 11]]],
    'a colour list without a page count' => [['colored_pages' => [1, 2]]],
]);

test('a colour list with page numbers that are not whole pages is rejected before calling the AI', function (array $pages) {
    PrintRequestAgent::fake()->preventStrayPrompts();

    $this->postJson('/agent', ['request' => 'Print file ini', 'total_pages' => 10, 'colored_pages' => $pages])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['colored_pages.0' => 'Daftar halaman berwarna tidak valid. Lampirkan ulang file PDF-nya.']);

    PrintRequestAgent::assertNeverPrompted();
})->with([
    'zero' => [[0]],
    'text' => [['merah']],
    'negative' => [[-3]],
]);
