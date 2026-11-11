<?php

use App\Filament\Resources\Shops\Pages\CreateShop;
use App\Models\Shop;
use App\Models\User;
use Livewire\Livewire;

test('admin can create a shop with services and opening hours', function () {
    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(CreateShop::class)
        ->fillForm([
            'name' => 'Print Sejahtera',
            'address' => 'Ruko Taman Gapura, CitraLand',
            'latitude' => -7.2875310,
            'longitude' => 112.6370120,
            'whatsapp' => '6281234567890',
            'services' => ['print', 'photocopy'],
            'opening_hours' => [
                'mon' => [['open' => '08:00', 'close' => '21:00']],
            ],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $shop = Shop::where('slug', 'print-sejahtera')->sole();

    expect($shop->opening_hours['mon'])->toBe([['open' => '08:00', 'close' => '21:00']])
        ->and($shop->services->map->value->all())->toBe(['print', 'photocopy']);
});

test('shop name is required', function () {
    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(CreateShop::class)
        ->fillForm(['name' => '', 'address' => 'x', 'latitude' => 1, 'longitude' => 1, 'services' => []])
        ->call('create')
        ->assertHasFormErrors(['name' => 'required']);
});

test('regular users cannot open the shop form', function () {
    $this->actingAs(User::factory()->create())
        ->get('/admin/shops/create')
        ->assertForbidden();
});

test('whatsapp number must be in international format', function () {
    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(CreateShop::class)
        ->fillForm(['name' => 'Print X', 'address' => 'x', 'latitude' => -7.29, 'longitude' => 112.64, 'whatsapp' => '0812-3456', 'services' => []])
        ->call('create')
        ->assertHasFormErrors(['whatsapp']);
});

test('shop coordinates must be inside the CitraLand area', function () {
    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(CreateShop::class)
        ->fillForm(['name' => 'Print Jauh', 'address' => 'x', 'latitude' => -6.2, 'longitude' => 106.8, 'services' => []])
        ->call('create')
        ->assertHasFormErrors(['latitude', 'longitude']);
});
