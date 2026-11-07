<?php

use App\Enums\UserRole;
use App\Models\User;

test('create admin command makes an admin account', function () {
    $this->artisan('cariprint:create-admin', [
        'email' => 'owner@example.test',
        '--password' => 'secret-pass',
    ])->assertSuccessful();

    expect(User::where('email', 'owner@example.test')->sole()->role)->toBe(UserRole::Admin);
});

test('create admin command refuses an empty password', function () {
    $this->artisan('cariprint:create-admin', [
        'email' => 'owner@example.test',
        '--password' => '',
    ])->assertFailed();

    expect(User::count())->toBe(0);
});
