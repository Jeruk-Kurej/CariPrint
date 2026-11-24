<?php

use App\Enums\PriceSource;
use App\Enums\ReportStatus;
use App\Enums\SuggestionStatus;
use App\Filament\Widgets\PendingSuggestions;
use App\Filament\Widgets\ShopOverview;
use App\Models\PriceList;
use App\Models\Report;
use App\Models\Shop;
use App\Models\ShopSuggestion;
use App\Models\User;
use Livewire\Livewire;

beforeEach(fn () => $this->actingAs(User::factory()->admin()->create()));

test('the dashboard counts published shops, drafts, unconfirmed prices, waiting suggestions, and waiting reports', function () {
    $shop = Shop::factory()->create();
    Shop::factory()->count(3)->draft()->create();
    PriceList::factory()->for($shop)->create(['source' => PriceSource::Estimated]);
    PriceList::factory()->for($shop)->verified()->create();
    ShopSuggestion::factory()->count(2)->create();
    ShopSuggestion::factory()->create(['status' => SuggestionStatus::Rejected]);
    Report::factory()->for($shop)->count(3)->create();
    Report::factory()->for($shop)->create(['status' => ReportStatus::Resolved]);

    Livewire::test(ShopOverview::class)
        ->assertSee('Tempat printing')
        ->assertSee('Draf menunggu diperiksa')
        ->assertSee('Harga belum dikonfirmasi')
        ->assertSee('Usulan menunggu')
        ->assertSee('Laporan menunggu');

    $stats = (new ReflectionMethod(ShopOverview::class, 'getStats'))->invoke(new ShopOverview);

    expect(collect($stats)->map(fn ($stat) => (int) $stat->getValue())->all())->toBe([1, 3, 1, 2, 3]);
});

test('only suggestions that wait for a decision are listed on the dashboard', function () {
    $waiting = ShopSuggestion::factory()->create(['name' => 'Masih Menunggu']);
    $done = ShopSuggestion::factory()->create(['name' => 'Sudah Diterima', 'status' => SuggestionStatus::Accepted]);

    Livewire::test(PendingSuggestions::class)
        ->assertCanSeeTableRecords([$waiting])
        ->assertCanNotSeeTableRecords([$done]);
});

test('the admin dashboard page opens', function () {
    $this->get('/admin')->assertOk()->assertSee('Usulan tempat');
});
