<?php

use App\Enums\ColorNeed;
use App\Services\Pricing\ColorCheck;

test('a page is judged by the share of it that is coloured', function (float $share, ColorNeed $expected) {
    expect((new ColorCheck)->need($share))->toBe($expected);
})->with([
    'no colour' => [0.0, ColorNeed::BlackWhite],
    'exactly at the black and white limit' => [0.02, ColorNeed::BlackWhite],
    'just over the limit' => [0.021, ColorNeed::Maybe],
    'a coloured heading' => [0.07, ColorNeed::Maybe],
    'just under the colour limit' => [0.099, ColorNeed::Maybe],
    'exactly at the colour limit' => [0.10, ColorNeed::Color],
    'a photo' => [0.6, ColorNeed::Color],
]);

test('every page of a file is classified in order', function () {
    expect((new ColorCheck)->classify([0.0, 0.05, 0.4, 0.01]))
        ->toBe([ColorNeed::BlackWhite, ColorNeed::Maybe, ColorNeed::Color, ColorNeed::BlackWhite]);
});

test('only the pages that clearly need colour are suggested for colour', function () {
    expect((new ColorCheck)->suggestedColorPages([0.0, 0.05, 0.4, 0.01, 0.9]))->toBe([3, 5]);
});

test('every page with some colour counts as coloured, doubtful pages included', function () {
    expect((new ColorCheck)->coloredPages([0.0, 0.05, 0.4, 0.01, 0.9]))->toBe([2, 3, 5]);
});

test('a file without colour suggests nothing', function () {
    $check = new ColorCheck;

    expect($check->suggestedColorPages([0.0, 0.0, 0.01]))->toBe([])
        ->and($check->coloredPages([0.0, 0.0, 0.01]))->toBe([]);
});

test('the limits come from the configuration', function () {
    config(['cariprint.color_check.bw_up_to' => 0.2, 'cariprint.color_check.color_from' => 0.5]);

    $check = new ColorCheck;

    expect($check->need(0.15))->toBe(ColorNeed::BlackWhite)
        ->and($check->need(0.3))->toBe(ColorNeed::Maybe)
        ->and($check->need(0.5))->toBe(ColorNeed::Color);
});
