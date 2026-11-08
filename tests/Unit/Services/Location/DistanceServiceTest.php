<?php

use App\Services\Location\DistanceService;

test('distance between the same point is zero', function () {
    expect((new DistanceService)->kilometers(-7.2875, 112.6370, -7.2875, 112.6370))->toEqualWithDelta(0.0, 0.0001);
});

test('distance between two known points in Surabaya is about right', function () {
    // Tunjungan Plaza to Stasiun Gubeng, roughly 2.5 km straight-line
    $km = (new DistanceService)->kilometers(-7.2627, 112.7383, -7.2653, 112.7513);

    expect($km)->toBeGreaterThan(0.9)->toBeLessThan(1.3);
});

test('distance is symmetric', function () {
    $service = new DistanceService;

    expect($service->kilometers(-7.2, 112.6, -7.3, 112.7))
        ->toEqualWithDelta($service->kilometers(-7.3, 112.7, -7.2, 112.6), 0.0001);
});
