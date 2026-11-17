<?php

use App\Enums\UserRole;
use App\Models\User;
use Laravel\Socialite\Socialite;
use Laravel\Socialite\Two\User as GoogleUser;

function fakeGoogle(array $attributes = []): void
{
    Socialite::fake('google', GoogleUser::fake([
        'id' => 'google-123',
        'name' => 'Siti Aminah',
        'email' => 'siti@gmail.com',
        'email_verified' => true,
        ...$attributes,
    ]));
}

test('the login page offers Google sign-in', function () {
    $this->get('/login')->assertOk();
});

test('the Google button sends the visitor to Google', function () {
    Socialite::fake('google');

    $this->get('/masuk/google')->assertRedirect();
});

test('a new Google visitor becomes a plain user and is signed in', function () {
    fakeGoogle();

    $this->get('/masuk/google/callback')->assertRedirect(route('home'));

    $user = User::where('email', 'siti@gmail.com')->sole();

    expect($user->role)->toBe(UserRole::User)
        ->and($user->google_id)->toBe('google-123')
        ->and($user->name)->toBe('Siti Aminah')
        ->and($user->email_verified_at)->not->toBeNull();
    $this->assertAuthenticatedAs($user);
});

test('a returning Google visitor reuses the same account', function () {
    fakeGoogle();

    $this->get('/masuk/google/callback');
    auth()->logout();
    $this->get('/masuk/google/callback');

    expect(User::where('email', 'siti@gmail.com')->count())->toBe(1);
});

test('signing in with Google goes back to the page the visitor wanted', function () {
    fakeGoogle();

    $this->get('/usul-tempat')->assertRedirect('/login');
    $this->get('/masuk/google/callback')->assertRedirect('/usul-tempat');
});

test('an existing account with the same email is linked, and an admin stays an admin', function () {
    $admin = User::factory()->admin()->create(['email' => 'siti@gmail.com']);
    fakeGoogle();

    $this->get('/masuk/google/callback');

    expect($admin->refresh()->google_id)->toBe('google-123')
        ->and($admin->role)->toBe(UserRole::Admin)
        ->and(User::count())->toBe(1);
    $this->assertAuthenticatedAs($admin);
});

test('an email tied to a different Google account is refused', function () {
    User::factory()->create(['email' => 'siti@gmail.com', 'google_id' => 'google-OTHER']);
    fakeGoogle();

    $this->get('/masuk/google/callback')->assertRedirect('/login')->assertSessionHasErrors('google');
    $this->assertGuest();
});

test('a Google email that is not verified is refused', function () {
    fakeGoogle(['email_verified' => false]);

    $this->get('/masuk/google/callback')->assertRedirect('/login')->assertSessionHasErrors('google');

    expect(User::count())->toBe(0);
    $this->assertGuest();
});

test('a declined or failed Google sign-in shows a message instead of an error page', function () {
    Socialite::fake('google', fn () => throw new RuntimeException('access_denied'));

    $this->get('/masuk/google/callback')->assertRedirect('/login')->assertSessionHasErrors('google');
    $this->assertGuest();
});

test('public registration with a password stays closed', function () {
    $this->post('/register', ['name' => 'X', 'email' => 'x@example.com', 'password' => 'password-123', 'password_confirmation' => 'password-123'])
        ->assertNotFound();
});
