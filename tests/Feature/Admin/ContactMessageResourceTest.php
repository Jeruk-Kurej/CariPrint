<?php

use App\Enums\ContactStatus;
use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Filament\Resources\ContactMessages\Pages\EditContactMessage;
use App\Filament\Resources\ContactMessages\Pages\ListContactMessages;
use App\Models\ContactMessage;
use App\Models\User;
use Livewire\Livewire;

beforeEach(fn () => $this->actingAs(User::factory()->admin()->create()));

test('the message list page opens for an admin', function () {
    $this->get('/admin/contact-messages')->assertOk()->assertSee('Pesan masuk');
});

test('an admin sees the messages and can filter by status', function () {
    $new = ContactMessage::factory()->create();
    $done = ContactMessage::factory()->create(['status' => ContactStatus::Done]);

    Livewire::test(ListContactMessages::class)
        ->assertCanSeeTableRecords([$new, $done])
        ->filterTable('status', ContactStatus::New->value)
        ->assertCanSeeTableRecords([$new])
        ->assertCanNotSeeTableRecords([$done]);
});

test('the review page shows the sender and the message', function () {
    $message = ContactMessage::factory()->create(['email' => 'siti@example.com', 'message' => 'Tolong bantu akunku yang terkunci.']);

    Livewire::test(EditContactMessage::class, ['record' => $message->getRouteKey()])
        ->assertFormSet([
            'email' => 'siti@example.com',
            'message' => 'Tolong bantu akunku yang terkunci.',
            'category_label' => $message->category->label(),
        ]);
});

test('an admin marks a message and writes a note', function () {
    $message = ContactMessage::factory()->create();

    Livewire::test(EditContactMessage::class, ['record' => $message->getRouteKey()])
        ->fillForm(['status' => ContactStatus::Replied->value, 'admin_note' => 'Sudah dibalas lewat email.'])
        ->call('save')
        ->assertHasNoFormErrors();

    $message->refresh();

    expect($message->status)->toBe(ContactStatus::Replied)
        ->and($message->admin_note)->toBe('Sudah dibalas lewat email.');
});

test('the menu shows how many messages are new', function () {
    ContactMessage::factory()->count(2)->create();
    ContactMessage::factory()->create(['status' => ContactStatus::Done]);

    expect(ContactMessageResource::getNavigationBadge())->toBe('2');
});

test('a plain user cannot open the message list', function () {
    auth()->logout();
    $this->actingAs(User::factory()->create())->get('/admin/contact-messages')->assertForbidden();
});
