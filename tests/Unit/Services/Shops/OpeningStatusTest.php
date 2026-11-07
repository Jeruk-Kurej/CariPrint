<?php

use App\Enums\OpenState;
use App\Services\Shops\OpeningStatus;
use Carbon\CarbonImmutable;

function at(string $when): CarbonImmutable
{
    return CarbonImmutable::parse($when, 'Asia/Jakarta');
}

function hours(array $days): array
{
    $week = ['mon' => [], 'tue' => [], 'wed' => [], 'thu' => [], 'fri' => [], 'sat' => [], 'sun' => []];

    return array_merge($week, $days);
}

beforeEach(fn () => config(['cariprint.directory.closing_soon_minutes' => 30]));

test('shop is open during opening hours', function () {
    $hours = hours(['sat' => [['open' => '08:00', 'close' => '21:00']]]);

    $result = (new OpeningStatus)->evaluate($hours, at('2026-11-07 12:00'));

    expect($result->state)->toBe(OpenState::Open)
        ->and($result->closesAt->format('H:i'))->toBe('21:00');
});

test('shop shows closing soon within the configured minutes before closing', function () {
    $hours = hours(['sat' => [['open' => '08:00', 'close' => '21:00']]]);

    expect((new OpeningStatus)->evaluate($hours, at('2026-11-07 20:45'))->state)
        ->toBe(OpenState::ClosingSoon);
});

test('closing exactly at close time means closed', function () {
    $hours = hours(['sat' => [['open' => '08:00', 'close' => '21:00']]]);

    expect((new OpeningStatus)->evaluate($hours, at('2026-11-07 21:00'))->state)
        ->toBe(OpenState::Closed);
});

test('shop before opening reports the next opening time', function () {
    $hours = hours(['sat' => [['open' => '08:00', 'close' => '21:00']]]);

    $result = (new OpeningStatus)->evaluate($hours, at('2026-11-07 06:30'));

    expect($result->state)->toBe(OpenState::Closed)
        ->and($result->opensAt->format('Y-m-d H:i'))->toBe('2026-11-07 08:00');
});

test('a closed day stays closed and finds the next day it opens', function () {
    $hours = hours(['mon' => [['open' => '08:00', 'close' => '17:00']]]);

    $result = (new OpeningStatus)->evaluate($hours, at('2026-11-07 12:00')); // Saturday

    expect($result->state)->toBe(OpenState::Closed)
        ->and($result->opensAt->format('Y-m-d H:i'))->toBe('2026-11-09 08:00');
});

test('overnight hours stay open after midnight', function () {
    // Friday 18:00 until Saturday 02:00
    $hours = hours(['fri' => [['open' => '18:00', 'close' => '02:00']]]);

    $result = (new OpeningStatus)->evaluate($hours, at('2026-11-07 01:00')); // Saturday, early hours

    expect($result->state)->toBe(OpenState::Open)
        ->and($result->closesAt->format('Y-m-d H:i'))->toBe('2026-11-07 02:00');
});

test('overnight hours are closed after they end', function () {
    $hours = hours(['fri' => [['open' => '18:00', 'close' => '02:00']]]);

    expect((new OpeningStatus)->evaluate($hours, at('2026-11-07 03:00'))->state)
        ->toBe(OpenState::Closed);
});

test('24 hour shop is always open', function () {
    $hours = hours(['sun' => [['open' => '00:00', 'close' => '24:00']]]);

    expect((new OpeningStatus)->evaluate($hours, at('2026-11-08 23:59'))->state)
        ->toBe(OpenState::Open);
});

test('split hours with a lunch break are closed during the break', function () {
    $hours = hours(['sat' => [
        ['open' => '08:00', 'close' => '12:00'],
        ['open' => '13:00', 'close' => '21:00'],
    ]]);

    expect((new OpeningStatus)->evaluate($hours, at('2026-11-07 12:30'))->state)
        ->toBe(OpenState::Closed);
});
