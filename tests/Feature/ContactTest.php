<?php

use App\Enums\ContactCategory;
use App\Enums\ContactStatus;
use App\Mail\NewContactMessage;
use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Inertia\Testing\AssertableInertia as Assert;

function contactPayload(array $overrides = []): array
{
    return ['category' => 'data', 'email' => 'siti@example.com', 'message' => 'Tolong hapus datakuku, tombol hapus akunnya tidak jalan.', ...$overrides];
}

test('the contact page is open to guests and lists the message types', function () {
    $this->get('/hubungi-kami')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('info/contact')
            ->where('email', null)
            ->where('sent', false)
            ->has('categories', count(ContactCategory::cases())));
});

test('a signed-in user is told which email the reply goes to', function () {
    $user = User::factory()->create(['email' => 'siti@example.com']);

    $this->actingAs($user)->get('/hubungi-kami')
        ->assertInertia(fn (Assert $page) => $page->where('email', 'siti@example.com'));
});

test('a guest can send a message with an email address', function () {
    Mail::fake();

    $this->post('/hubungi-kami', contactPayload())
        ->assertSessionHasNoErrors()
        ->assertRedirect('/hubungi-kami');

    $message = ContactMessage::sole();

    expect($message->email)->toBe('siti@example.com')
        ->and($message->user_id)->toBeNull()
        ->and($message->category)->toBe(ContactCategory::Data)
        ->and($message->status)->toBe(ContactStatus::New)
        ->and($message->message)->toBe('Tolong hapus datakuku, tombol hapus akunnya tidak jalan.');
});

test('a signed-in user writes from the account email, whatever the form says', function () {
    Mail::fake();
    $user = User::factory()->create(['email' => 'siti@example.com']);

    $this->actingAs($user)->post('/hubungi-kami', contactPayload(['email' => 'orang-lain@example.com']))
        ->assertSessionHasNoErrors();

    $message = ContactMessage::sole();

    expect($message->email)->toBe('siti@example.com')
        ->and($message->user_id)->toBe($user->id);
});

test('the thank you page shows once after sending', function () {
    Mail::fake();

    $this->post('/hubungi-kami', contactPayload());

    $this->get('/hubungi-kami')->assertInertia(fn (Assert $page) => $page->where('sent', true));
    $this->get('/hubungi-kami')->assertInertia(fn (Assert $page) => $page->where('sent', false));
});

test('the admins are emailed and can reply straight to the sender', function () {
    Mail::fake();
    config(['cariprint.admin.notify_emails' => ['admin@example.com']]);

    $this->post('/hubungi-kami', contactPayload());

    Mail::assertSent(NewContactMessage::class, fn (NewContactMessage $mail) => $mail->hasTo('admin@example.com')
        && $mail->hasReplyTo('siti@example.com')
        && str_contains($mail->envelope()->subject, 'Hapus atau ubah data'));
});

test('a failing email does not lose the message', function () {
    config(['cariprint.admin.notify_emails' => ['admin@example.com']]);
    Mail::shouldReceive('to')->andThrow(new RuntimeException('smtp down'));

    $this->post('/hubungi-kami', contactPayload())->assertSessionHasNoErrors();

    expect(ContactMessage::count())->toBe(1);
});

test('the form is validated', function (array $overrides, string $field) {
    $this->post('/hubungi-kami', contactPayload($overrides))->assertSessionHasErrors($field);

    expect(ContactMessage::count())->toBe(0);
})->with([
    'missing category' => [['category' => ''], 'category'],
    'unknown category' => [['category' => 'rahasia'], 'category'],
    'guest without email' => [['email' => ''], 'email'],
    'not an email' => [['email' => 'bukan-email'], 'email'],
    'empty message' => [['message' => ''], 'message'],
    'too short' => [['message' => 'halo'], 'message'],
    'too long' => [['message' => str_repeat('a', 1001)], 'message'],
    'the trap field is filled' => [['website' => 'https://spam.example'], 'website'],
]);

test('the validation messages are in Indonesian', function () {
    $this->post('/hubungi-kami', contactPayload(['category' => '', 'email' => '', 'message' => 'halo']))
        ->assertSessionHasErrors([
            'category' => 'Pilih jenis pesannya dulu.',
            'email' => 'Isi emailmu supaya kami bisa membalas.',
            'message' => 'Ceritakan sedikit lebih jelas, minimal 10 karakter.',
        ]);
});

test('a visitor cannot set the status or the note', function () {
    Mail::fake();

    $this->post('/hubungi-kami', contactPayload(['status' => 'done', 'admin_note' => 'Sudah beres', 'user_id' => 99]));

    $message = ContactMessage::sole();

    expect($message->status)->toBe(ContactStatus::New)
        ->and($message->admin_note)->toBeNull()
        ->and($message->user_id)->toBeNull();
});

test('messages are limited to a few per hour', function () {
    Mail::fake();

    foreach (range(1, 5) as $attempt) {
        $this->post('/hubungi-kami', contactPayload())->assertSessionHasNoErrors();
    }

    $this->post('/hubungi-kami', contactPayload())->assertStatus(429);
});
