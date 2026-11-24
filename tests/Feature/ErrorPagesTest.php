<?php

use Illuminate\Support\Facades\Route;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->withoutVite();

    Route::middleware('web')->group(function () {
        Route::get('/__forbidden', fn () => abort(403));
        Route::get('/__expired', fn () => abort(419));
        Route::get('/__broken', fn () => throw new RuntimeException('boom'));
    });
});

test('an unknown address renders the public error page with a 404', function () {
    $this->get('/rute-yang-tidak-ada')
        ->assertNotFound()
        ->assertSee('info\/error', false)
        ->assertInertia(fn (Assert $page) => $page->component('info/error')->where('status', 404));
});

test('an unknown shop renders the public error page with a 404', function () {
    $this->get('/shops/tempat-yang-tidak-ada')
        ->assertNotFound()
        ->assertInertia(fn (Assert $page) => $page->component('info/error')->where('status', 404));
});

test('forbidden and expired pages render the public error page', function (string $path, int $status) {
    $this->get($path)
        ->assertStatus($status)
        ->assertInertia(fn (Assert $page) => $page->component('info/error')->where('status', $status));
})->with([
    'forbidden' => ['/__forbidden', 403],
    'expired' => ['/__expired', 419],
]);

test('a server error renders the public error page when debug is off', function () {
    config(['app.debug' => false]);

    $this->get('/__broken')
        ->assertStatus(500)
        ->assertInertia(fn (Assert $page) => $page->component('info/error')->where('status', 500));
});

test('a server error keeps the debug page when debug is on', function () {
    config(['app.debug' => true]);

    $this->get('/__broken')->assertStatus(500)->assertSee('RuntimeException');
});

test('json requests and the admin panel keep the default error responses', function () {
    $this->getJson('/rute-yang-tidak-ada')->assertNotFound()->assertHeaderMissing('X-Inertia');

    $this->get('/admin/halaman-yang-tidak-ada')->assertDontSee('info\/error', false);
});
