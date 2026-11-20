<?php

use App\Enums\ShopStatus;
use App\Models\Shop;
use App\Services\Survey\ApifyDraftImporter;
use App\Services\Survey\SurveyArea;

function importFixture(?SurveyArea $area = null): array
{
    return app(ApifyDraftImporter::class)->import(
        base_path('tests/Fixtures/apify/places.csv'),
        $area ?? SurveyArea::circle(-7.285, 112.64, 8),
    );
}

test('places inside the radius become drafts and the rest are counted as skipped', function () {
    $summary = importFixture();

    expect($summary['created'])->toBe(4)
        ->and($summary['withoutHours'])->toBe(1)
        ->and($summary['skipped'])->toHaveKeys(['bukan tempat print atau fotokopi', 'di luar area radius 8 km', 'tutup permanen'])
        ->and(Shop::where('status', ShopStatus::Draft->value)->count())->toBe(4)
        ->and(Shop::published()->count())->toBe(0);
});

test('a draft keeps the hours, services, and place id from the export', function () {
    importFixture();

    $shop = Shop::where('place_id', 'ChIJsejahtera')->sole();

    expect($shop->status)->toBe(ShopStatus::Draft)
        ->and($shop->opening_hours['tue'])->toBe([['open' => '08:00', 'close' => '11:00'], ['open' => '15:00', 'close' => '21:00']])
        ->and($shop->opening_hours['sun'])->toBe([])
        ->and($shop->hasOpeningHours())->toBeTrue()
        ->and(Shop::where('place_id', 'ChIJtanpajam')->sole()->hasOpeningHours())->toBeFalse();
});

test('importing the same file twice adds nothing the second time', function () {
    importFixture();

    $second = importFixture();

    expect($second['created'])->toBe(0)
        ->and($second['existing'])->toBe(4)
        ->and(Shop::count())->toBe(4);
});

test('a shop that is already published is never overwritten', function () {
    Shop::factory()->create(['name' => 'Nama Yang Sudah Diperiksa', 'slug' => 'print-sejahtera', 'place_id' => 'ChIJsejahtera']);

    importFixture();

    $shop = Shop::where('place_id', 'ChIJsejahtera')->sole();

    expect($shop->name)->toBe('Nama Yang Sudah Diperiksa')
        ->and($shop->status)->toBe(ShopStatus::Published);
});

test('two different places with the same name get different slugs', function () {
    Shop::factory()->create(['slug' => 'print-sejahtera', 'place_id' => 'ChIJlain']);

    importFixture();

    expect(Shop::where('place_id', 'ChIJsejahtera')->sole()->slug)->toBe('print-sejahtera-2')
        ->and(Shop::where('place_id', 'ChIJkedua')->sole()->slug)->not->toBe('print-sejahtera-2');
});

test('a smaller radius keeps only the places close to the centre', function () {
    $summary = importFixture(SurveyArea::circle(-7.285, 112.64, 1));

    expect($summary['created'])->toBe(2)
        ->and(Shop::pluck('place_id')->sort()->values()->all())->toBe(['ChIJrumahan', 'ChIJsejahtera']);
});
