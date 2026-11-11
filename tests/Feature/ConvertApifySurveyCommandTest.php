<?php

use App\Models\Shop;
use App\Services\Survey\SurveyImporter;

test('the command writes survey csvs and a whatsapp contact sheet', function () {
    $out = sys_get_temp_dir().'/apify-'.uniqid();

    $this->artisan('survey:convert-apify', ['file' => base_path('tests/Fixtures/apify/places.csv'), '--out' => $out])
        ->expectsOutputToContain('3 tempat siap diimpor, 1 perlu dicek jam bukanya.')
        ->assertSuccessful();

    $shops = array_map('str_getcsv', file("$out/survey-shops.csv", FILE_IGNORE_NEW_LINES));
    $contacts = file_get_contents("$out/kontak-whatsapp.csv");

    expect($shops)->toHaveCount(4)
        ->and(file("$out/survey-prices.csv"))->toHaveCount(1)
        ->and($contacts)->toContain('https://wa.me/6281234567890?text=')
        ->and($contacts)->toContain('Nomor telepon rumah')
        ->and($contacts)->toContain('Google tidak punya jam buka');
});

test('the contact link carries the shop name in the message', function () {
    $out = sys_get_temp_dir().'/apify-'.uniqid();

    $this->artisan('survey:convert-apify', ['file' => base_path('tests/Fixtures/apify/places.csv'), '--out' => $out]);

    expect(file_get_contents("$out/kontak-whatsapp.csv"))->toContain(rawurlencode('Boleh minta info untuk Print Sejahtera?'));
});

test('the converted files import cleanly with the survey importer', function () {
    $out = sys_get_temp_dir().'/apify-'.uniqid();

    $this->artisan('survey:convert-apify', ['file' => base_path('tests/Fixtures/apify/places.csv'), '--out' => $out]);

    $count = (new SurveyImporter)->import("$out/survey-shops.csv", "$out/survey-prices.csv");
    $shop = Shop::where('slug', 'print-sejahtera')->sole();

    expect($count)->toBe(3)
        ->and($shop->place_id)->toBe('ChIJsejahtera')
        ->and($shop->opening_hours['tue'])->toBe([['open' => '08:00', 'close' => '11:00'], ['open' => '15:00', 'close' => '21:00']])
        ->and($shop->opening_hours['sun'])->toBe([])
        ->and($shop->priceLists)->toBeEmpty();
});
