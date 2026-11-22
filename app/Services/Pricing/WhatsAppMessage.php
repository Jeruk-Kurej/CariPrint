<?php

namespace App\Services\Pricing;

use App\Enums\ShopService;

/**
 * Writes the first WhatsApp message a student sends to a shop. The wording is fixed here and every number comes
 * from the PrintSpec and the calculated total, never from AI (core rule 1).
 */
class WhatsAppMessage
{
    private const BINDING_LABELS = [
        'spiral_binding' => 'jilid spiral',
        'tape_binding' => 'jilid lakban',
        'hardcover_binding' => 'jilid hardcover',
    ];

    public function compose(PrintSpec $spec, int $total): string
    {
        $lines = [
            'Halo kak, aku mau print dengan detail begini ya:',
            '- Kertas '.strtoupper($spec->paperSize->value),
            ...$this->pageLines($spec),
            "- {$spec->copies} rangkap",
        ];

        if ($spec->binding !== null) {
            $lines[] = '- '.$this->bindingLabel($spec->binding);
        }

        $lines[] = '';
        $lines[] = 'Kalau dihitung dari daftar harga di CariPrint, totalnya sekitar '.$this->rupiah($total).'. Masih sesuai ya, kak?';
        $lines[] = 'Makasih banyak!';

        return implode("\n", $lines);
    }

    /**
     * @return list<string>
     */
    private function pageLines(PrintSpec $spec): array
    {
        $lines = [];

        if ($spec->bwPages > 0) {
            $lines[] = "- {$spec->bwPages} halaman hitam-putih".$this->where($spec->pages?->blackWhiteRuns() ?? []);
        }

        if ($spec->colorPages > 0) {
            $lines[] = "- {$spec->colorPages} halaman warna".$this->where($spec->pages?->colorRuns() ?? []);
        }

        return $lines;
    }

    /**
     * @param  list<array{from: int, to: int}>  $runs
     */
    private function where(array $runs): string
    {
        if ($runs === []) {
            return '';
        }

        $text = implode(', ', array_map(
            fn (array $run) => $run['from'] === $run['to'] ? (string) $run['from'] : "{$run['from']}-{$run['to']}",
            $runs,
        ));

        return " (hal. {$text})";
    }

    private function bindingLabel(ShopService $binding): string
    {
        return self::BINDING_LABELS[$binding->value] ?? $binding->name;
    }

    private function rupiah(int $amount): string
    {
        return 'Rp '.number_format($amount, 0, ',', '.');
    }
}
