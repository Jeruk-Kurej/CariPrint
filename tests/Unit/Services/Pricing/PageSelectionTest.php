<?php

use App\Services\Pricing\PageSelection;

test('neighbouring pages are joined into one run and a lone page stays on its own', function () {
    $selection = new PageSelection(blackWhite: [1, 2, 3, 7], color: [9, 10]);

    expect($selection->blackWhiteRuns())->toBe([['from' => 1, 'to' => 3], ['from' => 7, 'to' => 7]])
        ->and($selection->colorRuns())->toBe([['from' => 9, 'to' => 10]]);
});

test('a colour mode without pages has no runs', function () {
    $selection = new PageSelection(blackWhite: [4], color: []);

    expect($selection->colorRuns())->toBe([]);
});
