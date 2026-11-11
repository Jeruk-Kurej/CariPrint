<?php

namespace Database\Seeders;

use App\Services\Survey\SurveyImporter;
use Illuminate\Database\Seeder;

/**
 * Loads the surveyed shops from the folder in config('cariprint.survey.directory'). Skips quietly until the survey CSVs exist.
 */
class SurveyShopSeeder extends Seeder
{
    public function run(SurveyImporter $importer): void
    {
        $directory = config('cariprint.survey.directory');
        $shops = "$directory/survey-shops.csv";
        $prices = "$directory/survey-prices.csv";

        if (! is_file($shops) || ! is_file($prices)) {
            $this->command?->warn('Belum ada survey-shops.csv dan survey-prices.csv di $directory. Survei tidak diimpor.');

            return;
        }

        $count = $importer->import($shops, $prices);
        $this->command?->info("Survei diimpor: $count tempat.");
    }
}
