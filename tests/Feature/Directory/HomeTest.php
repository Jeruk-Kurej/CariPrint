<?php

use App\Models\Shop;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    Shop::factory()->create(['name' => 'Zeta Print', 'latitude' => -7.2875, 'longitude' => 112.6370]);
    Shop::factory()->create(['name' => 'Alfa Print', 'latitude' => -7.3100, 'longitude' => 112.6700]);
});

test('home lists shops by name when no location is given', function () {
    $this->get('/')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('directory/index')
            ->has('shops', 2)
            ->where('shops.0.name', 'Alfa Print')
            ->where('shops.0.distanceKm', null));
});

test('nearby sorts shops from the closest to the farthest', function () {
    $this->post('/shops/nearby', ['latitude' => -7.2870, 'longitude' => 112.6375])
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('directory/index')
            ->where('shops.0.name', 'Zeta Print')
            ->where('shops.1.name', 'Alfa Print')
            ->whereType('shops.0.distanceKm', 'double'));
});

test('nearby rejects invalid coordinates', function () {
    $this->postJson('/shops/nearby', ['latitude' => 200, 'longitude' => 112.6])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['latitude']);
});

test('coordinates are never in the URL', function () {
    $this->post('/shops/nearby', ['latitude' => -7.2870, 'longitude' => 112.6375]);

    expect(request()->fullUrl())->not->toContain('latitude');
});
