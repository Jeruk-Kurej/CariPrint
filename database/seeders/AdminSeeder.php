<?php

namespace Database\Seeders;

use App\Services\Admin\AdminAccount;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('cariprint.admin.email');
        $password = config('cariprint.admin.password');

        if (blank($email) || blank($password)) {
            $this->command?->warn('ADMIN_EMAIL or ADMIN_PASSWORD is empty in .env. Admin not created.');

            return;
        }

        AdminAccount::save($email, $password);
    }
}
