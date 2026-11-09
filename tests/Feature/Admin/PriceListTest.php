<?php

use App\Enums\PriceSource;
use App\Enums\ShopService;
use App\Filament\Resources\Shops\Pages\EditShop;
use App\Filament\Resources\Shops\RelationManagers\PriceListsRelationManager;
use App\Models\PriceList;
use App\Models\Shop;
use App\Models\User;
use Carbon\CarbonImmutable;
use Livewire\Livewire;

beforeEach(fn () => $this->actingAs(User::factory()->admin()->create()));

test('admin adds a price to a shop from the relation manager', function () {
    $shop = Shop::factory()->create();

    Livewire::test(PriceListsRelationManager::class, ['ownerRecord' => $shop, 'pageClass' => EditShop::class])
        ->callTableAction('create', data: [
            'service' => 'photocopy',
            'paper_size' => 'a4',
            'color_mode' => 'bw',
            'price' => 300,
            'unit' => 'page',
            'source' => 'verified',
        ])
        ->assertHasNoTableActionErrors();

    expect($shop->priceLists()->sole())
        ->service->toBe(ShopService::Photocopy)
        ->price->toBe(300)
        ->source->toBe(PriceSource::Verified);
});

test('changing a price moves the shop updated date', function () {
    CarbonImmutable::setTestNow('2026-11-08 09:00:00');
    $shop = Shop::factory()->has(PriceList::factory()->state(['price' => 500]))->create();

    CarbonImmutable::setTestNow('2026-11-08 12:00:00');
    $shop->priceLists()->sole()->update(['price' => 600]);

    expect($shop->fresh()->updated_at->format('H:i'))->toBe('12:00');
    CarbonImmutable::setTestNow();
});

test('price must be a whole number of rupiah', function () {
    $shop = Shop::factory()->create();

    Livewire::test(PriceListsRelationManager::class, ['ownerRecord' => $shop, 'pageClass' => EditShop::class])
        ->callTableAction('create', data: [
            'service' => 'print', 'paper_size' => 'a4', 'color_mode' => 'bw',
            'price' => 'lima ratus', 'unit' => 'page', 'source' => 'estimated',
        ])
        ->assertHasTableActionErrors(['price']);
});
