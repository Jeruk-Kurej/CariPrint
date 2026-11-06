<?php

use App\Enums\ReportStatus;
use App\Enums\UserRole;
use App\Models\PrintCheck;
use App\Models\Report;
use App\Models\User;

test('new users get the user role', function () {
    $user = User::factory()->create()->fresh();

    expect($user->role)->toBe(UserRole::User)
        ->and($user->isAdmin())->toBeFalse();
});

test('admin state marks the user as admin', function () {
    expect(User::factory()->admin()->create()->isAdmin())->toBeTrue();
});

test('role cannot be mass assigned', function () {
    $user = User::factory()->create();

    $user->fill(['role' => UserRole::Admin->value])->save();

    expect($user->fresh()->isAdmin())->toBeFalse();
});

test('user has reports and print checks', function () {
    $user = User::factory()
        ->has(Report::factory())
        ->has(PrintCheck::factory())
        ->create();

    expect($user->reports()->sole()->status)->toBe(ReportStatus::Pending)
        ->and($user->printChecks()->sole()->color_pages)->toBe([1, 4, 7]);
});
