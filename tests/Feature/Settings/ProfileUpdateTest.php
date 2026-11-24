<?php

use App\Models\ShopSuggestion;
use App\Models\User;

test('profile page is displayed', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get(route('profile.edit'));

    $response->assertOk();
});

test('profile information can be updated', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch(route('profile.update'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('profile.edit'));

    $user->refresh();

    expect($user->name)->toBe('Test User');
    expect($user->email)->toBe('test@example.com');
    expect($user->email_verified_at)->toBeNull();
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch(route('profile.update'), [
            'name' => 'Test User',
            'email' => $user->email,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('profile.edit'));

    expect($user->refresh()->email_verified_at)->not->toBeNull();
});

test('user can delete their account by typing their email', function () {
    $user = User::factory()->create(['email' => 'siti@example.com']);

    $response = $this
        ->actingAs($user)
        ->delete(route('profile.destroy'), ['email' => 'siti@example.com']);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('home'));

    $this->assertGuest();
    expect($user->fresh())->toBeNull();
});

test('the typed email ignores case and surrounding spaces', function () {
    $user = User::factory()->create(['email' => 'siti@example.com']);

    $this->actingAs($user)
        ->delete(route('profile.destroy'), ['email' => '  Siti@Example.com '])
        ->assertSessionHasNoErrors();

    expect($user->fresh())->toBeNull();
});

test('deleting an account also deletes the suggestions sent from its email', function () {
    $user = User::factory()->create(['email' => 'siti@example.com']);
    ShopSuggestion::factory()->count(2)->create(['contact_email' => 'siti@example.com']);
    $others = ShopSuggestion::factory()->create(['contact_email' => 'orang-lain@example.com']);

    $this->actingAs($user)->delete(route('profile.destroy'), ['email' => 'siti@example.com']);

    expect(ShopSuggestion::where('contact_email', 'siti@example.com')->count())->toBe(0)
        ->and($others->fresh())->not->toBeNull();
});

test('a wrong or missing email keeps the account and its suggestions', function (?string $typed) {
    $user = User::factory()->create(['email' => 'siti@example.com']);
    $suggestion = ShopSuggestion::factory()->create(['contact_email' => 'siti@example.com']);

    $this->actingAs($user)
        ->from(route('profile.edit'))
        ->delete(route('profile.destroy'), ['email' => $typed])
        ->assertSessionHasErrors('email')
        ->assertRedirect(route('profile.edit'));

    expect($user->fresh())->not->toBeNull()
        ->and($suggestion->fresh())->not->toBeNull();
})->with([
    'another email' => ['budi@example.com'],
    'empty' => [''],
    'missing' => [null],
]);

test('an admin cannot delete the account from here', function () {
    $admin = User::factory()->admin()->create(['email' => 'admin@example.com']);

    $this->actingAs($admin)
        ->delete(route('profile.destroy'), ['email' => 'admin@example.com'])
        ->assertForbidden();

    expect($admin->fresh())->not->toBeNull();
});
