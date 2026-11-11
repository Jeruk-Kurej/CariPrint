<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call(AdminSeeder::class);

        // Contoh toko hanya dipakai selama data survei belum ada, supaya tidak tercampur dengan data nyata.
        if (! is_file(config('cariprint.survey.directory').'/survey-shops.csv')) {
            $this->call(ShopSeeder::class);
        }

        $this->call(SurveyShopSeeder::class);
    }
}
