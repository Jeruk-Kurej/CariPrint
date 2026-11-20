<?php

use App\Services\Survey\SurveyArea;

test('a box contains the points inside it and nothing outside', function () {
    $area = SurveyArea::box(config('cariprint.area'), 'CitraLand');

    expect($area->contains(-7.285, 112.64))->toBeTrue()
        ->and($area->contains(-7.20, 112.70))->toBeFalse();
});

test('a circle is measured in kilometres from its centre', function () {
    $area = SurveyArea::circle(-7.285, 112.64, 5);

    expect($area->contains(-7.285, 112.65))->toBeTrue()
        ->and($area->contains(-7.285, 112.70))->toBeFalse()
        ->and($area->label)->toBe('radius 5 km');
});
