<?php

use App\Enums\PaperSize;
use App\Enums\ShopService;
use App\Services\Pricing\PageSelection;
use App\Services\Pricing\PrintSpec;
use App\Services\Pricing\WhatsAppMessage;

test('the message lists the paper, pages, copies and the estimated total', function () {
    $message = (new WhatsAppMessage)->compose(new PrintSpec(PaperSize::F4, bwPages: 20, colorPages: 5, copies: 2), 12500);

    expect($message)->toContain('Halo kak')
        ->toContain('- Kertas F4')
        ->toContain('- 20 halaman hitam-putih')
        ->toContain('- 5 halaman warna')
        ->toContain('- 2 rangkap')
        ->toContain('Rp 12.500');
});

test('binding is written in everyday words and left out when not asked for', function () {
    $writer = new WhatsAppMessage;

    expect($writer->compose(new PrintSpec(PaperSize::A4, 10, 0, binding: ShopService::SpiralBinding), 5000))->toContain('- jilid spiral')
        ->and($writer->compose(new PrintSpec(PaperSize::A4, 10, 0), 5000))->not->toContain('jilid');
});

test('a colour or black and white line is left out when there are no such pages', function () {
    $message = (new WhatsAppMessage)->compose(new PrintSpec(PaperSize::A4, bwPages: 10, colorPages: 0), 5000);

    expect($message)->toContain('hitam-putih')->not->toContain('warna');
});

test('page positions are written when the user pointed at specific pages', function () {
    $pages = new PageSelection(blackWhite: [4, 5, 6, 9], color: [1, 2, 3]);
    $message = (new WhatsAppMessage)->compose(new PrintSpec(PaperSize::A4, bwPages: 4, colorPages: 3, pages: $pages), 9000);

    expect($message)->toContain('- 4 halaman hitam-putih (hal. 4-6, 9)')
        ->toContain('- 3 halaman warna (hal. 1-3)');
});
