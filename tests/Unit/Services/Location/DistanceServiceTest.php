<?php

use App\Services\Location\DistanceService;

test('distance between the same point is zero', function () {
    expect((new DistanceService)->kilometers(-7.2875, 112.6370, -7.2875, 112.6370))->toEqualWithDelta(0.0, 0.0001);
});

test('one degree of latitude is about 111 km', function () {
    $km = (new DistanceService)->kilometers(0.0, 112.0, 1.0, 112.0);

    expect($km)->toEqualWithDelta(111.19, 0.05);
});

test('distance is symmetric', function () {
    $service = new DistanceService;

    expect($service->kilometers(-7.2, 112.6, -7.3, 112.7))
        ->toEqualWithDelta($service->kilometers(-7.3, 112.7, -7.2, 112.6), 0.0001);
});
