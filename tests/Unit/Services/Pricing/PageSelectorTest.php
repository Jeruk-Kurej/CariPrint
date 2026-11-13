<?php

use App\Enums\ColorMode;
use App\Services\Pricing\PageRange;
use App\Services\Pricing\PageSelector;
use App\Services\Pricing\UnreadablePrintRequest;

test('a numbered range gives exactly those pages', function () {
    $selection = (new PageSelector)->select([new PageRange(8, 10, ColorMode::Color)], totalPages: null);

    expect($selection->color)->toBe([8, 9, 10])
        ->and($selection->blackWhite)->toBe([]);
});

test('negative positions count from the end of the document', function () {
    $selection = (new PageSelector)->select([new PageRange(-3, -1, ColorMode::BlackWhite)], totalPages: 24);

    expect($selection->blackWhite)->toBe([22, 23, 24]);
});

test('a range without a colour mode leaves its pages out', function () {
    $selection = (new PageSelector)->select([
        new PageRange(1, -1, ColorMode::BlackWhite),
        new PageRange(-1, -1, null),
    ], totalPages: 4);

    expect($selection->blackWhite)->toBe([1, 2, 3])
        ->and($selection->color)->toBe([]);
});

test('a later range decides the colour of the pages it shares with an earlier one', function () {
    $selection = (new PageSelector)->select([
        new PageRange(1, -1, ColorMode::BlackWhite),
        new PageRange(1, 5, ColorMode::Color),
    ], totalPages: 8);

    expect($selection->color)->toBe([1, 2, 3, 4, 5])
        ->and($selection->blackWhite)->toBe([6, 7, 8]);
});

test('a range written backwards is read in page order', function () {
    $selection = (new PageSelector)->select([new PageRange(10, 8, ColorMode::BlackWhite)], totalPages: null);

    expect($selection->blackWhite)->toBe([8, 9, 10]);
});

test('pages counted from the end cannot be found without the document length', function () {
    (new PageSelector)->select([new PageRange(1, -1, ColorMode::BlackWhite)], totalPages: null);
})->throws(UnreadablePrintRequest::class, 'Asisten perlu tahu jumlah halaman dokumenmu');

test('a position outside the document is refused', function (int $from, int $to) {
    (new PageSelector)->select([new PageRange($from, $to, ColorMode::BlackWhite)], totalPages: 30);
})->with([
    'past the last page' => [1, 50],
    'page zero' => [0, 5],
    'more pages from the end than the document has' => [-31, -1],
])->throws(UnreadablePrintRequest::class, 'Dokumenmu 30 halaman');

test('a page number above the limit is refused when the document length is unknown', function () {
    config(['cariprint.uploads.max_pages' => 100]);

    (new PageSelector)->select([new PageRange(1, 101, ColorMode::BlackWhite)], totalPages: null);
})->throws(UnreadablePrintRequest::class, 'halaman 1 sampai 100');

test('a document longer than the limit is refused', function () {
    config(['cariprint.uploads.max_pages' => 100]);

    (new PageSelector)->select([new PageRange(1, 1, ColorMode::BlackWhite)], totalPages: 101);
})->throws(UnreadablePrintRequest::class, 'halaman 1 sampai 100');
