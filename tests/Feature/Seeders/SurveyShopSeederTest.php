<?php

use App\Enums\PriceSource;
use App\Models\PriceList;
use App\Models\Shop;
use App\Services\Survey\SurveyImporter;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\SurveyShopSeeder;

beforeEach(fn () => $this->fixtures = base_path('tests/Fixtures/survey'));

test('survey import creates shops with parsed hours, services and verified prices', function () {
    $count = (new SurveyImporter)->import("{$this->fixtures}/shops.csv", "{$this->fixtures}/prices.csv");

    $shop = Shop::where('slug', 'print-sejahtera')->sole();

    expect($count)->toBe(1)
        ->and($shop->opening_hours['mon'])->toBe([['open' => '08:00', 'close' => '12:00'], ['open' => '13:00', 'close' => '21:00']])
        ->and($shop->opening_hours['sun'])->toBe([])
        ->and($shop->services->map->value->all())->toBe(['print', 'photocopy', 'spiral_binding'])
        ->and($shop->last_surveyed_at)->not->toBeNull()
        ->and($shop->priceLists)->toHaveCount(3)
        ->and($shop->priceLists->every(fn (PriceList $p) => $p->source === PriceSource::Verified))->toBeTrue();
});

test('running the import twice updates rows instead of duplicating them', function () {
    $importer = new SurveyImporter;

    $importer->import("{$this->fixtures}/shops.csv", "{$this->fixtures}/prices.csv");
    $importer->import("{$this->fixtures}/shops.csv", "{$this->fixtures}/prices.csv");

    expect(Shop::count())->toBe(1)
        ->and(PriceList::count())->toBe(3);
});

test('unknown service code stops the import with a clear message', function () {
    $bad = tempnam(sys_get_temp_dir(), 'shops');
    file_put_contents($bad, file_get_contents("{$this->fixtures}/shops.csv"));
    file_put_contents($bad, str_replace('print|photocopy|spiral_binding', 'print|jasa-aneh', file_get_contents($bad)));

    expect(fn () => (new SurveyImporter)->import($bad, "{$this->fixtures}/prices.csv"))
        ->toThrow(InvalidArgumentException::class, 'Kode layanan tidak dikenal: jasa-aneh');
});

test('seeder skips quietly when the survey files are not there yet', function () {
    config(['cariprint.survey.directory' => sys_get_temp_dir().'/no-survey-'.uniqid()]);

    $this->seed(SurveyShopSeeder::class);

    expect(Shop::count())->toBe(0);
});

test('sample shops are not seeded once real survey data exists', function () {
    $directory = sys_get_temp_dir().'/survey-'.uniqid();
    mkdir($directory);
    copy(base_path('tests/Fixtures/survey/shops.csv'), "$directory/survey-shops.csv");
    copy(base_path('tests/Fixtures/survey/prices.csv'), "$directory/survey-prices.csv");
    config(['cariprint.survey.directory' => $directory]);

    $this->seed(DatabaseSeeder::class);

    expect(Shop::where('slug', 'like', 'contoh-%')->exists())->toBeFalse()
        ->and(Shop::where('slug', 'print-sejahtera')->exists())->toBeTrue();
});
