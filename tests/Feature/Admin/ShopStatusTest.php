<?php

use App\Enums\ShopStatus;
use App\Filament\Resources\Shops\Pages\CreateShop;
use App\Filament\Resources\Shops\Pages\ListShops;
use App\Models\Shop;
use App\Models\User;
use Livewire\Livewire;

beforeEach(fn () => $this->actingAs(User::factory()->admin()->create()));

test('a new shop is published unless the admin saves it as a draft', function () {
    Livewire::test(CreateShop::class)
        ->fillForm([
            'name' => 'Print Draf',
            'address' => 'Ruko Taman Gapura, CitraLand',
            'latitude' => -7.2875310,
            'longitude' => 112.6370120,
            'status' => ShopStatus::Draft->value,
            'services' => ['print'],
            'opening_hours' => ['mon' => [['open' => '08:00', 'close' => '21:00']]],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Shop::where('slug', 'print-draf')->sole()->status)->toBe(ShopStatus::Draft);
});

test('the admin can still see drafts in the shop list and filter by status', function () {
    $published = Shop::factory()->create();
    $draft = Shop::factory()->draft()->create();

    Livewire::test(ListShops::class)
        ->assertCanSeeTableRecords([$published, $draft])
        ->filterTable('status', ShopStatus::Draft->value)
        ->assertCanSeeTableRecords([$draft])
        ->assertCanNotSeeTableRecords([$published]);
});

test('publishing a draft makes it visible, and published shops have no publish action', function () {
    $draft = Shop::factory()->draft()->create();
    $published = Shop::factory()->create();

    Livewire::test(ListShops::class)
        ->assertTableActionHidden('publish', $published)
        ->callTableAction('publish', $draft);

    expect($draft->refresh()->status)->toBe(ShopStatus::Published);
});

test('several drafts can be published at once', function () {
    $drafts = Shop::factory()->count(3)->draft()->create();

    Livewire::test(ListShops::class)
        ->callTableBulkAction('publish', $drafts);

    expect(Shop::published()->count())->toBe(3);
});

test('a draft without opening hours cannot be published', function () {
    $noHours = Shop::factory()->draft()->create(['opening_hours' => ['mon' => [], 'tue' => [], 'wed' => [], 'thu' => [], 'fri' => [], 'sat' => [], 'sun' => []]]);

    Livewire::test(ListShops::class)
        ->callTableAction('publish', $noHours)
        ->assertNotified('1 tempat belum diterbitkan');

    expect($noHours->refresh()->status)->toBe(ShopStatus::Draft);
});

test('a bulk publish skips the drafts without hours and publishes the rest', function () {
    $ready = Shop::factory()->draft()->create();
    $noHours = Shop::factory()->draft()->create(['opening_hours' => ['mon' => [], 'tue' => [], 'wed' => [], 'thu' => [], 'fri' => [], 'sat' => [], 'sun' => []]]);

    Livewire::test(ListShops::class)->callTableBulkAction('publish', [$ready, $noHours]);

    expect($ready->refresh()->status)->toBe(ShopStatus::Published)
        ->and($noHours->refresh()->status)->toBe(ShopStatus::Draft);
});

test('the shop form accepts a place anywhere in the city but not outside it', function () {
    Livewire::test(CreateShop::class)
        ->fillForm(['name' => 'Jauh', 'address' => 'Rungkut', 'latitude' => -7.33, 'longitude' => 112.78, 'services' => ['print'], 'opening_hours' => ['mon' => [['open' => '08:00', 'close' => '21:00']]]])
        ->call('create')
        ->assertHasNoFormErrors();

    Livewire::test(CreateShop::class)
        ->fillForm(['name' => 'Luar Kota', 'address' => 'Malang', 'latitude' => -7.98, 'longitude' => 112.63, 'services' => ['print'], 'opening_hours' => ['mon' => [['open' => '08:00', 'close' => '21:00']]]])
        ->call('create')
        ->assertHasFormErrors(['latitude']);
});

test('the admin can filter shops by whether opening hours are filled', function () {
    $filled = Shop::factory()->draft()->create();
    $empty = Shop::factory()->draft()->create(['opening_hours' => ['mon' => [], 'tue' => [], 'wed' => [], 'thu' => [], 'fri' => [], 'sat' => [], 'sun' => []]]);

    Livewire::test(ListShops::class)
        ->filterTable('has_hours', false)
        ->assertCanSeeTableRecords([$empty])
        ->assertCanNotSeeTableRecords([$filled])
        ->filterTable('has_hours', true)
        ->assertCanSeeTableRecords([$filled])
        ->assertCanNotSeeTableRecords([$empty]);
});

test('the admin can search shops by name or address', function () {
    $byName = Shop::factory()->create(['name' => 'Print Mawar', 'address' => 'Jl. Pertama']);
    $byAddress = Shop::factory()->create(['name' => 'Cetak Cepat', 'address' => 'Jl. Kenanga, Lakarsantri']);

    Livewire::test(ListShops::class)
        ->searchTable('Lakarsantri')
        ->assertCanSeeTableRecords([$byAddress])
        ->assertCanNotSeeTableRecords([$byName]);
});

test('drafts that are not print shops can be deleted in bulk', function () {
    $drafts = Shop::factory()->count(3)->draft()->create();
    $keep = Shop::factory()->create();

    Livewire::test(ListShops::class)->callTableBulkAction('delete', $drafts);

    expect(Shop::count())->toBe(1)->and(Shop::first()->is($keep))->toBeTrue();
});

test('each shop links to its Google Maps listing', function () {
    $withPlace = Shop::factory()->create(['name' => 'Print A', 'place_id' => 'ChIJabc']);
    $withoutPlace = Shop::factory()->create(['place_id' => null, 'latitude' => -7.25, 'longitude' => 112.7]);

    Livewire::test(ListShops::class)
        ->assertTableActionHasUrl('maps', 'https://www.google.com/maps/search/?api=1&query=Print%20A&query_place_id=ChIJabc', $withPlace)
        ->assertTableActionHasUrl('maps', 'https://www.google.com/maps/search/?api=1&query=-7.25,112.7', $withoutPlace);
});
