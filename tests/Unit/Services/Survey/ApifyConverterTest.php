<?php

use App\Services\Survey\ApifyConverter;

beforeEach(function () {
    $this->result = (new ApifyConverter)->convert(
        base_path('tests/Fixtures/apify/places.csv'),
        config('cariprint.area'),
    );
    $this->shops = collect($this->result['shops'])->keyBy('slug');
});

test('print and copy shops inside the area are converted, others are skipped with a reason', function () {
    expect($this->shops->keys()->all())->toBe(['print-sejahtera', 'copy-rumahan', 'print-sejahtera-2'])
        ->and(collect($this->result['skipped'])->pluck('reason', 'title')->all())->toBe([
            'Jasa 3D Printing Warna' => 'bukan tempat print atau fotokopi',
            'BCA KCP' => 'bukan tempat print atau fotokopi',
            'Print Jauh' => 'di luar area CitraLand',
            'Print Tutup Permanen' => 'tutup permanen',
        ]);
});

test('google opening hours become the survey format', function () {
    $hours = $this->shops['print-sejahtera'];

    expect($hours['mon'])->toBe('08:00-12:00')
        ->and($hours['tue'])->toBe('08:00-11:00|15:00-21:00')
        ->and($hours['wed'])->toBe('tutup')
        ->and($hours['thu'])->toBe('00:00-24:00')
        ->and($hours['sun'])->toBe('tutup');
});

test('only mobile numbers become a whatsapp number', function () {
    expect($this->shops['print-sejahtera']['whatsapp'])->toBe('6281234567890')
        ->and($this->shops['copy-rumahan']['whatsapp'])->toBe('');
});

test('services follow the categories and the place id is kept', function () {
    expect($this->shops['print-sejahtera']['services'])->toBe('print|photocopy')
        ->and($this->shops['copy-rumahan']['services'])->toBe('print')
        ->and($this->shops['print-sejahtera']['place_id'])->toBe('ChIJsejahtera')
        ->and($this->shops['print-sejahtera']['surveyed_at'])->toBe('2026-11-10');
});

test('a shop without any opening hours needs a check instead of being imported as closed', function () {
    expect($this->result['needsCheck'])->toHaveCount(1)
        ->and($this->result['needsCheck'][0]['name'])->toBe('Tanpa Jam');
});

test('an unreadable hours format stops the conversion with a clear message', function () {
    $bad = tempnam(sys_get_temp_dir(), 'apify');
    file_put_contents($bad, str_replace('08.00 to 12.00', 'pagi sampai siang', file_get_contents(base_path('tests/Fixtures/apify/places.csv'))));

    expect(fn () => (new ApifyConverter)->convert($bad, config('cariprint.area')))
        ->toThrow(InvalidArgumentException::class, 'Format jam tidak dikenal');
});
