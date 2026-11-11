<?php

use App\Ai\Agents\PriceListReaderAgent;
use App\Models\Shop;

beforeEach(function () {
    $this->photos = sys_get_temp_dir().'/photos-'.uniqid();
    mkdir("{$this->photos}/print-sejahtera", 0755, true);
    mkdir("{$this->photos}/tidak-ada", 0755, true);

    $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR4nGNgYGD4DwABBAEAHnOcbwAAAABJRU5ErkJggg==');
    file_put_contents("{$this->photos}/print-sejahtera/daftar.png", $png);
    file_put_contents("{$this->photos}/tidak-ada/daftar.png", $png);
});

test('the command writes a draft csv with a check column and skips unknown shops', function () {
    Shop::factory()->create(['slug' => 'print-sejahtera']);
    PriceListReaderAgent::fake([['items' => [
        ['service' => 'print', 'paper_size' => 'a4', 'color_mode' => 'bw', 'price' => 500, 'text' => 'Print A4 BW', 'confident' => true],
        ['service' => 'print', 'paper_size' => 'a4', 'color_mode' => 'color', 'price' => 1500, 'text' => 'Warna 1.500', 'confident' => false],
    ]]]);
    $out = sys_get_temp_dir().'/draft-'.uniqid();

    $this->artisan('survey:read-prices', ['folder' => $this->photos, '--out' => $out])
        ->expectsOutputToContain('print-sejahtera: 2 harga dibaca dari 1 foto, 1 perlu diperiksa.')
        ->expectsOutputToContain("tidak ada tempat dengan slug 'tidak-ada'")
        ->assertSuccessful();

    $rows = array_map('str_getcsv', file("$out/survey-prices-draft.csv", FILE_IGNORE_NEW_LINES));

    expect($rows)->toHaveCount(3)
        ->and($rows[0])->toContain('periksa')
        ->and($rows[2][4])->toBe('1500')
        ->and($rows[2][7])->toBe('ya');
});

test('a missing folder fails with a clear message', function () {
    $this->artisan('survey:read-prices', ['folder' => '/tidak/ada'])
        ->expectsOutputToContain('Folder tidak ditemukan')
        ->assertFailed();
});
