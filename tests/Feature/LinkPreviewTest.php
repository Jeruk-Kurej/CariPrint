<?php

use App\Enums\ShopService;
use App\Models\PriceList;
use App\Models\Shop;

test('every page carries the default link preview tags with an absolute image', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('<meta property="og:title" content="'.config('app.name').': cari tempat print yang buka dekat kampus">', false)
        ->assertSee('<meta property="og:image" content="'.asset('og-image.png').'">', false)
        ->assertSee('<meta name="twitter:card" content="summary_large_image">', false)
        ->assertSee('<meta name="description" content="Cek harga, jam buka, dan jarak tempat print', false);
});

test('the preview image is shipped and sized for link previews', function () {
    $path = public_path('og-image.png');

    expect(file_exists($path))->toBeTrue()
        ->and(getimagesize($path))->toMatchArray([1200, 630])
        ->and(filesize($path))->toBeLessThan(300 * 1024);
});

test('a shop page previews its own name, starting price, and address', function () {
    $shop = Shop::factory()->create(['name' => 'Print Sejahtera', 'address' => 'Ruko Taman Gapura A-5, CitraLand.']);
    PriceList::factory()->create(['shop_id' => $shop->id, 'service' => ShopService::Print, 'price' => 1500]);

    $this->get("/shops/{$shop->slug}")
        ->assertOk()
        ->assertSee('<meta property="og:title" content="Print Sejahtera - '.config('app.name').'">', false)
        ->assertSee('Print mulai dari Rp 1.500. Ruko Taman Gapura A-5, CitraLand. Cek jam buka, daftar harga, dan rutenya.', false);
});

test('a shop without a print price leaves the price out of its preview', function () {
    $shop = Shop::factory()->create(['name' => 'Tanpa Harga', 'address' => 'Jl. Contoh 1']);

    $this->get("/shops/{$shop->slug}")
        ->assertSee('<meta name="description" content="Jl. Contoh 1. Cek jam buka, daftar harga, dan rutenya.">', false);
});

test('a shop name with quotes cannot break out of the meta tag', function () {
    $shop = Shop::factory()->create(['name' => 'Print "Jaya" <script>x</script>']);

    $this->get("/shops/{$shop->slug}")
        ->assertDontSee('<script>x</script>', false)
        ->assertSee('Print &quot;Jaya&quot; &lt;script&gt;x&lt;/script&gt; - '.config('app.name'), false);
});
