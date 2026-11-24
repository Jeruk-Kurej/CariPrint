<?php

use App\Models\User;
use Illuminate\Support\Arr;

test('the default language is Indonesian', function () {
    expect(config('app.locale'))->toBe('id');
});

test('every framework language line has an Indonesian version', function (string $file) {
    $english = require base_path("vendor/laravel/framework/src/Illuminate/Translation/lang/en/{$file}.php");
    $indonesian = require base_path("lang/id/{$file}.php");

    // The English attributes list is empty, so it flattens to a single key that the filled Indonesian list does not have.
    $missing = array_diff(array_keys(Arr::dot(Arr::except($english, 'attributes'))), array_keys(Arr::dot($indonesian)));

    expect($missing)->toBe([]);
})->with(['auth', 'passwords', 'pagination', 'validation']);

test('no Indonesian language line is left in English', function () {
    $lines = Arr::dot(require base_path('lang/id/validation.php'));

    $english = collect($lines)->filter(fn ($line) => is_string($line) && preg_match('/\b(field|must|the|is required)\b/i', $line));

    expect($english->all())->toBe([]);
});

test('the suggestion form reports missing fields in Indonesian', function () {
    $this->actingAs(User::factory()->create())->post('/usul-tempat', [])
        ->assertSessionHasErrors([
            'name' => 'Nama tempat wajib diisi.',
            'address' => 'Alamat wajib diisi.',
        ]);
});

test('a wrong password on the login form says so in Indonesian', function () {
    $user = User::factory()->create();

    $this->post(route('login.store'), ['email' => $user->email, 'password' => 'salah-total'])
        ->assertSessionHasErrors(['email' => 'Email atau kata sandi salah.']);
});

test('changing the password with a wrong current password says so in Indonesian', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->put(route('user-password.update'), [
        'current_password' => 'bukan-kata-sandinya',
        'password' => 'KataSandiBaru123!',
        'password_confirmation' => 'KataSandiBaru123!',
    ])->assertSessionHasErrors(['current_password' => 'Kata sandinya salah.']);
});

test('an invalid email on the profile page is explained in Indonesian', function () {
    $this->actingAs(User::factory()->create())->patch(route('profile.update'), ['name' => 'Siti', 'email' => 'bukan-email'])
        ->assertSessionHasErrors(['email' => 'Alamat email harus berupa alamat email yang valid.']);
});

test('a mismatching password confirmation is explained in Indonesian', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->put(route('user-password.update'), [
        'current_password' => 'password',
        'password' => 'KataSandiBaru123!',
        'password_confirmation' => 'beda',
    ])->assertSessionHasErrors(['password' => 'Konfirmasi kata sandi tidak cocok.']);
});
