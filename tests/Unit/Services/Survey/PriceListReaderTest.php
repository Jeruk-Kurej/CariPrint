<?php

use App\Ai\Agents\PriceListReaderAgent;
use App\Services\Survey\PriceListReader;

/** A real 1x1 PNG, so the image attachment has a file to point at. */
function tinyPhoto(): string
{
    $path = tempnam(sys_get_temp_dir(), 'foto').'.png';
    file_put_contents($path, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR4nGNgYGD4DwABBAEAHnOcbwAAAABJRU5ErkJggg=='));

    return $path;
}

function item(array $overrides = []): array
{
    return array_merge([
        'service' => 'print', 'paper_size' => 'a4', 'color_mode' => 'bw', 'price' => 500,
        'text' => 'Print A4 BW 500', 'confident' => true,
    ], $overrides);
}

test('a clear print line becomes a row without the check flag', function () {
    PriceListReaderAgent::fake([['items' => [item()]]]);

    $rows = (new PriceListReader)->read('print-sejahtera', [tinyPhoto()]);

    expect($rows)->toHaveCount(1)
        ->and($rows[0])->toMatchArray([
            'shop_slug' => 'print-sejahtera', 'service' => 'print', 'paper_size' => 'a4',
            'color_mode' => 'bw', 'price' => '500', 'unit' => 'page', 'periksa' => '',
        ]);
});

test('binding and laminating get the item unit and no paper or colour', function () {
    PriceListReaderAgent::fake([['items' => [
        item(['service' => 'spiral_binding', 'paper_size' => 'a4', 'color_mode' => 'bw', 'price' => 7000]),
        item(['service' => 'laminating', 'paper_size' => null, 'color_mode' => null, 'price' => 3000]),
    ]]]);

    $rows = (new PriceListReader)->read('x', [tinyPhoto()]);

    expect($rows[0])->toMatchArray(['unit' => 'item', 'paper_size' => '', 'color_mode' => '', 'periksa' => ''])
        ->and($rows[1])->toMatchArray(['unit' => 'item', 'periksa' => '']);
});

test('rows the AI was unsure about or that miss a needed field are flagged for a person to check', function () {
    PriceListReaderAgent::fake([['items' => [
        item(['confident' => false]),
        item(['service' => 'photocopy', 'paper_size' => null, 'price' => 300]),
        item(['service' => 'print', 'color_mode' => 'color', 'price' => 9_999_999]),
    ]]]);

    $rows = (new PriceListReader)->read('x', [tinyPhoto()]);

    expect(array_column($rows, 'periksa'))->toBe(['ya', 'ya', 'ya']);
});

test('an unknown service is dropped and never invented', function () {
    PriceListReaderAgent::fake([['items' => [item(['service' => 'sablon'])]]]);

    expect((new PriceListReader)->read('x', [tinyPhoto()]))->toBe([]);
});

test('different prices for the same service across two photos are both flagged', function () {
    PriceListReaderAgent::fake([
        ['items' => [item(['price' => 500])]],
        ['items' => [item(['price' => 700])]],
    ]);

    $rows = (new PriceListReader)->read('x', [tinyPhoto(), tinyPhoto()]);

    expect(array_column($rows, 'periksa'))->toBe(['ya', 'ya']);
});
