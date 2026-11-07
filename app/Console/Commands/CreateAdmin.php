<?php

namespace App\Console\Commands;

use App\Services\Admin\AdminAccount;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('cariprint:create-admin {email : Email for the admin account} {--password= : Password (asked securely if omitted)}')]
#[Description('Create or promote an admin account. Public registration is disabled.')]
class CreateAdmin extends Command
{
    public function handle(): int
    {
        $password = $this->option('password') ?? $this->secret('Password');

        if (blank($password)) {
            $this->error('Password cannot be empty.');

            return self::FAILURE;
        }

        AdminAccount::save($this->argument('email'), $password);

        $this->info('Admin account saved.');

        return self::SUCCESS;
    }
}
