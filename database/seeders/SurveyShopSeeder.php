<?php

namespace Database\Seeders;

use App\Services\Survey\SurveyImporter;
use Illuminate\Database\Seeder;

/**
 * Loads the surveyed shops from database/data. Skips quietly until the survey CSVs exist.
 */
class SurveyShopSeeder extends Seeder
{
    public function run(SurveyImporter $importer): void
    {
        $shops = database_path('data/survey-shops.csv');
        $prices = database_path('data/survey-prices.csv');

        if (! is_file($shops) || ! is_file($prices)) {
            $this->command?->warn('Belum ada database/data/survey-shops.csv dan survey-prices.csv. Survei tidak diimpor.');

            return;
        }

        $count = $importer->import($shops, $prices);
        $this->command?->info("Survei diimpor: $count tempat.");
    }
}
