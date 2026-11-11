<?php

namespace App\Console\Commands;

use App\Models\Shop;
use App\Services\Survey\CsvFile;
use App\Services\Survey\PriceListReader;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Throwable;

#[Signature('survey:read-prices {folder : Folder berisi satu subfolder per tempat, nama subfolder = slug tempat} {--out= : Folder hasil (default storage/app/survey)}')]
#[Description('Baca foto daftar harga dengan AI jadi CSV draf untuk diperiksa manusia sebelum diimpor.')]
class ReadPricePhotos extends Command
{
    private const COLUMNS = ['shop_slug', 'service', 'paper_size', 'color_mode', 'price', 'unit', 'tulisan_di_foto', 'periksa', 'foto'];

    public function handle(PriceListReader $reader): int
    {
        $folder = rtrim($this->argument('folder'), '/');

        if (! is_dir($folder)) {
            $this->error("Folder tidak ditemukan: $folder");

            return self::FAILURE;
        }

        $drafts = [];

        foreach (glob("$folder/*", GLOB_ONLYDIR) ?: [] as $shopFolder) {
            $slug = basename($shopFolder);

            if (! Shop::where('slug', $slug)->exists()) {
                $this->warn("Dilewati: tidak ada tempat dengan slug '$slug'.");

                continue;
            }

            $photos = glob("$shopFolder/*.{jpg,jpeg,png,webp,heic,heif,JPG,JPEG,PNG,WEBP,HEIC}", GLOB_BRACE) ?: [];

            if ($photos === []) {
                $this->warn("Dilewati: tidak ada foto di folder '$slug'.");

                continue;
            }

            try {
                $rows = $reader->read($slug, $photos);
            } catch (Throwable $exception) {
                report($exception);
                $this->error("Gagal membaca foto '$slug': {$exception->getMessage()}");

                continue;
            }

            $this->line(sprintf('%s: %d harga dibaca dari %d foto, %d perlu diperiksa.', $slug, count($rows), count($photos), count(array_filter($rows, fn (array $row) => $row['periksa'] === 'ya'))));
            array_push($drafts, ...$rows);
        }

        return $this->write($drafts);
    }

    /**
     * @param  list<array<string, string>>  $drafts
     */
    private function write(array $drafts): int
    {
        $out = $this->option('out') ?: storage_path('app/survey');

        if (! is_dir($out)) {
            mkdir($out, 0755, true);
        }

        CsvFile::write("$out/survey-prices-draft.csv", self::COLUMNS, $drafts);

        $this->info("Draf ada di: $out/survey-prices-draft.csv. Periksa baris bertanda 'ya', hapus yang salah, lalu salin ke database/data/survey-prices.csv.");

        return self::SUCCESS;
    }
}
