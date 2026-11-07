<?php

use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\AdminSeeder;
use Illuminate\Support\Facades\Hash;

test('admin seeder creates an admin from config', function () {
    config(['cariprint.admin.email' => 'admin@example.test', 'cariprint.admin.password' => 'secret-pass']);

    $this->seed(AdminSeeder::class);

    $admin = User::where('email', 'admin@example.test')->sole();
    expect($admin->role)->toBe(UserRole::Admin)
        ->and(Hash::check('secret-pass', $admin->password))->toBeTrue();
});

test('running the admin seeder twice does not duplicate the admin', function () {
    config(['cariprint.admin.email' => 'admin@example.test', 'cariprint.admin.password' => 'secret-pass']);

    $this->seed(AdminSeeder::class);
    $this->seed(AdminSeeder::class);

    expect(User::where('email', 'admin@example.test')->count())->toBe(1);
});

test('admin seeder does nothing when credentials are missing', function () {
    config(['cariprint.admin.email' => null, 'cariprint.admin.password' => null]);

    $this->seed(AdminSeeder::class);

    expect(User::count())->toBe(0);
});
