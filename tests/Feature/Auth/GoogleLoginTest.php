<?php

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Exceptions;
use Laravel\Socialite\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
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

    $this->get('/usul-tempat')->assertOk();
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

test('an account made by the create-admin command takes the Google name, but a chosen name is kept', function (string $genericName) {
    $generic = User::factory()->admin()->create(['email' => 'siti@gmail.com', 'name' => $genericName]);
    fakeGoogle(['name' => 'Siti Aminah']);
    $this->get('/masuk/google/callback');
    expect($generic->refresh()->name)->toBe('Siti Aminah');

    auth()->logout();
    $generic->forceFill(['google_id' => null, 'name' => 'Nama Pilihan Sendiri'])->save();
    $this->get('/masuk/google/callback');
    expect($generic->refresh()->name)->toBe('Nama Pilihan Sendiri');
})->with(['the current default' => ['Admin'], 'the earlier default' => ['Admin CariPrint']]);

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

test('a declined Google sign-in says so and is not treated as an error', function () {
    Exceptions::fake();
    Socialite::fake('google');

    $this->get('/masuk/google/callback?error=access_denied')
        ->assertRedirect('/login')
        ->assertSessionHasErrors(['google' => 'Masuk dengan Google dibatalkan.']);

    Exceptions::assertNothingReported();
    $this->assertGuest();
});

test('an expired sign-in session asks the visitor to try again', function () {
    Socialite::fake('google', fn () => throw new InvalidStateException);

    $this->get('/masuk/google/callback')
        ->assertRedirect('/login')
        ->assertSessionHasErrors(['google' => 'Sesi masuk kedaluwarsa. Tekan Masuk dengan Google sekali lagi.']);
    $this->assertGuest();
});

test('an unexpected Google failure is logged so the cause can be found, and the visitor sees a plain message', function () {
    Exceptions::fake();
    Socialite::fake('google', fn () => throw new RuntimeException('invalid_client'));

    $this->get('/masuk/google/callback')
        ->assertRedirect('/login')
        ->assertSessionHasErrors(['google' => 'Masuk dengan Google gagal. Coba lagi beberapa menit lagi.']);

    Exceptions::assertReported(fn (RuntimeException $exception) => $exception->getMessage() === 'invalid_client');
    $this->assertGuest();
});

test('public registration with a password stays closed', function () {
    $this->post('/register', ['name' => 'X', 'email' => 'x@example.com', 'password' => 'password-123', 'password_confirmation' => 'password-123'])
        ->assertNotFound();
});
