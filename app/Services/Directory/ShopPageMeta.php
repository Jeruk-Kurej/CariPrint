<?php

namespace App\Services\Directory;

/**
 * The text that link previews (WhatsApp, Telegram, social apps) show for a shop page. Crawlers do not run
 * JavaScript, so this is rendered into the page head on the server instead of through the React page.
 */
class ShopPageMeta
{
    /**
     * @param  array<string, mixed>  $detail  The array built by ShopDirectory::detail().
     * @return array{title: string, description: string}
     */
    public function for(array $detail): array
    {
        $price = $detail['startingPrice'] ?? null;

        return [
            'title' => $detail['name'].' - '.config('app.name'),
            'description' => collect([
                is_int($price) ? 'Print mulai dari Rp '.number_format($price, 0, ',', '.') : null,
                rtrim((string) $detail['address'], '. '),
                'Cek jam buka, daftar harga, dan rutenya',
            ])->filter()->implode('. ').'.',
        ];
    }
}
