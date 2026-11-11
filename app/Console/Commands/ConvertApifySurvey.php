<?php

namespace App\Console\Commands;

use App\Services\Survey\ApifyConverter;
use App\Services\Survey\CsvFile;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('survey:convert-apify {file : CSV hasil export Apify Google Maps Scraper} {--out= : Folder hasil (default storage/app/survey)}')]
#[Description('Ubah export Apify jadi CSV survei, ditambah daftar kontak WhatsApp untuk meminta harga.')]
class ConvertApifySurvey extends Command
{
    private const SHOP_COLUMNS = [
        'slug', 'name', 'address', 'latitude', 'longitude', 'whatsapp', 'place_id',
        'mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun',
        'services', 'color_page_rule', 'surveyed_at', 'surveyor',
    ];

    private const PRICE_COLUMNS = ['shop_slug', 'service', 'paper_size', 'color_mode', 'price', 'unit'];

    public function handle(ApifyConverter $converter): int
    {
        $result = $converter->convert($this->argument('file'), config('cariprint.area'));
        $out = $this->option('out') ?: storage_path('app/survey');

        if (! is_dir($out)) {
            mkdir($out, 0755, true);
        }

        $this->writeCsv("$out/survey-shops.csv", self::SHOP_COLUMNS, $result['shops']);
        $this->writeCsv("$out/survey-prices.csv", self::PRICE_COLUMNS, []);
        $this->writeCsv("$out/kontak-whatsapp.csv", ['tempat', 'telepon_di_google', 'tautan_whatsapp', 'catatan'], $this->contacts($result));

        $this->info(count($result['shops']).' tempat siap diimpor, '.count($result['needsCheck']).' perlu dicek jam bukanya.');

        foreach ($result['skipped'] as $skipped) {
            $this->line("  dilewati: {$skipped['title']} ({$skipped['reason']})");
        }

        $this->line("Hasil ada di: $out");

        return self::SUCCESS;
    }

    /**
     * @param  array{shops: list<array<string, string>>, needsCheck: list<array<string, string>>}  $result
     * @return list<array<string, string>>
     */
    private function contacts(array $result): array
    {
        $rows = [];

        foreach ($result['shops'] as $shop) {
            $rows[] = $this->contact($shop, '');
        }

        foreach ($result['needsCheck'] as $shop) {
            $rows[] = $this->contact($shop, 'Google tidak punya jam buka, tanyakan juga jam bukanya. ');
        }

        return $rows;
    }

    /**
     * @param  array<string, string>  $shop
     * @return array<string, string>
     */
    private function contact(array $shop, string $note): array
    {
        $link = '';

        if ($shop['whatsapp'] !== '') {
            $message = str_replace('{tempat}', $shop['name'], config('cariprint.survey.whatsapp_message'));
            $link = "https://wa.me/{$shop['whatsapp']}?text=".rawurlencode($message);
        } else {
            $note .= $shop['phone'] === '' ? 'Tidak ada nomor, cari kontaknya (Instagram atau datang langsung).' : 'Nomor telepon rumah, hubungi lewat telepon.';
        }

        return [
            'tempat' => $shop['name'],
            'telepon_di_google' => $shop['phone'],
            'tautan_whatsapp' => $link,
            'catatan' => trim($note),
        ];
    }

    /**
     * @param  list<string>  $columns
     * @param  list<array<string, string>>  $rows
     */
    private function writeCsv(string $path, array $columns, array $rows): void
    {
        CsvFile::write($path, $columns, $rows);
    }
}
