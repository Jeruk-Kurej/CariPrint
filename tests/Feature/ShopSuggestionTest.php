<?php

use App\Enums\SuggestionStatus;
use App\Filament\Resources\ShopSuggestions\Pages\EditShopSuggestion;
use App\Filament\Resources\ShopSuggestions\Pages\ListShopSuggestions;
use App\Mail\NewShopSuggestion;
use App\Mail\SuggestionReceived;
use App\Mail\SuggestionReviewed;
use App\Models\Shop;
use App\Models\ShopSuggestion;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Inertia\Testing\AssertableInertia as Assert;
use Livewire\Livewire;

beforeEach(function () {
    $this->visitor = User::factory()->create(['email' => 'pengusul@example.com']);
    $this->actingAs($this->visitor);
    Mail::fake();
    config(['cariprint.admin.notify_emails' => ['admin@cariprint.test']]);
});

function suggestion(array $overrides = []): array
{
    return ['name' => 'Print Baru', 'address' => 'Ruko G-Walk Blok A', ...$overrides];
}

test('the suggestion form opens for a signed-in visitor and shows their email', function () {
    $this->get('/usul-tempat')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('suggestions/create')
            ->where('submitted', false)
            ->where('email', 'pengusul@example.com'));
});

test('a visitor who is not signed in can read the page but cannot submit', function () {
    auth()->logout();

    $this->get('/usul-tempat')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('suggestions/create')->where('email', null));
    $this->post('/usul-tempat', suggestion())->assertRedirect('/login');

    expect(ShopSuggestion::count())->toBe(0);
});

test('a guest who signs in from the suggestion page comes back to it', function () {
    auth()->logout();

    $this->get('/usul-tempat');

    expect(session('url.intended'))->toEndWith('/usul-tempat');
});

test('the receipt and decision go to the account email, whatever the form sends', function () {
    $this->post('/usul-tempat', suggestion(['contact_email' => 'orang-lain@example.com']));

    expect(ShopSuggestion::sole()->contact_email)->toBe('pengusul@example.com');
    Mail::assertSent(SuggestionReceived::class, fn (SuggestionReceived $mail) => $mail->hasTo('pengusul@example.com'));
    Mail::assertNotSent(SuggestionReceived::class, fn (SuggestionReceived $mail) => $mail->hasTo('orang-lain@example.com'));
});

test('a visitor suggestion is saved as pending and the admin is emailed', function () {
    $this->post('/usul-tempat', suggestion(['maps_url' => 'https://maps.app.goo.gl/abc', 'note' => 'Buka sampai malam']))
        ->assertRedirect('/usul-tempat');

    $saved = ShopSuggestion::sole();

    expect($saved->status)->toBe(SuggestionStatus::Pending)
        ->and($saved->note)->toBe('Buka sampai malam');

    Mail::assertSent(NewShopSuggestion::class, fn (NewShopSuggestion $mail) => $mail->hasTo('admin@cariprint.test')
        && $mail->suggestion->is($saved));
});

test('the visitor gets a receipt that says the suggestion waits for approval', function () {
    $this->post('/usul-tempat', suggestion());

    Mail::assertSent(SuggestionReceived::class, function (SuggestionReceived $mail) {
        $mail->assertSeeInHtml('menunggu persetujuan');
        $mail->assertSeeInHtml('Print Baru');

        return $mail->hasTo('pengusul@example.com');
    });
});

test('the page thanks the visitor after a suggestion is sent', function () {
    $this->followingRedirects()
        ->post('/usul-tempat', suggestion())
        ->assertInertia(fn (Assert $page) => $page->where('submitted', true));
});

test('a suggestion never creates a public shop', function () {
    $this->post('/usul-tempat', suggestion());

    expect(Shop::count())->toBe(0);
});

test('the email names the place and links to the admin review page', function () {
    $saved = ShopSuggestion::factory()->create(['name' => 'Print Baru']);

    (new NewShopSuggestion($saved))
        ->assertHasSubject('Usulan tempat baru: Print Baru')
        ->assertSeeInHtml('Print Baru')
        ->assertSeeInHtml("/admin/shop-suggestions/{$saved->id}/edit");
});

test('every configured admin address gets the email', function () {
    config(['cariprint.admin.notify_emails' => ['satu@cariprint.test', 'dua@cariprint.test']]);

    $this->post('/usul-tempat', suggestion());

    Mail::assertSent(NewShopSuggestion::class, fn (NewShopSuggestion $mail) => $mail->hasTo('satu@cariprint.test')
        && $mail->hasTo('dua@cariprint.test'));
});

test('the suggestion is kept when the email cannot be sent', function () {
    Mail::shouldReceive('to')->andThrow(new RuntimeException('SMTP mati'));

    $this->post('/usul-tempat', suggestion())->assertRedirect('/usul-tempat');

    expect(ShopSuggestion::count())->toBe(1);
});

test('no admin email is attempted when no admin address is configured', function () {
    config(['cariprint.admin.notify_emails' => []]);

    $this->post('/usul-tempat', suggestion());

    Mail::assertNotSent(NewShopSuggestion::class);
    expect(ShopSuggestion::count())->toBe(1);
});

test('invalid suggestions are rejected', function (array $payload, string $field) {
    $this->post('/usul-tempat', $payload)->assertSessionHasErrors($field);

    expect(ShopSuggestion::count())->toBe(0);
    Mail::assertNothingSent();
})->with([
    'no name' => [['address' => 'Jalan A'], 'name'],
    'no address' => [['name' => 'Print Baru'], 'address'],
    'a link that is not a web address' => [['name' => 'Print Baru', 'address' => 'Jalan A', 'maps_url' => 'javascript:alert(1)'], 'maps_url'],
    'the bot trap field filled in' => [['name' => 'Print Baru', 'address' => 'Jalan A', 'website' => 'http://spam.test'], 'website'],
]);

test('sending too many suggestions is throttled', function () {
    foreach (range(1, 5) as $attempt) {
        $this->post('/usul-tempat', suggestion())->assertRedirect();
    }

    $this->post('/usul-tempat', suggestion())->assertTooManyRequests();
});

test('only admins can open the suggestion list', function () {
    $this->actingAs(User::factory()->create())->get('/admin/shop-suggestions')->assertForbidden();
    $this->actingAs(User::factory()->admin()->create())->get('/admin/shop-suggestions')->assertOk();
});

test('an admin sees suggestions and can mark one as accepted', function () {
    $this->actingAs(User::factory()->admin()->create());
    $saved = ShopSuggestion::factory()->create();

    Livewire::test(ListShopSuggestions::class)->assertCanSeeTableRecords([$saved]);

    Livewire::test(EditShopSuggestion::class, ['record' => $saved->getRouteKey()])
        ->fillForm(['status' => SuggestionStatus::Accepted->value, 'admin_note' => 'Sudah ditambahkan'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($saved->refresh()->status)->toBe(SuggestionStatus::Accepted)
        ->and($saved->admin_note)->toBe('Sudah ditambahkan');
});

test('the visitor is told when an admin accepts the suggestion, with the admin note', function () {
    $saved = ShopSuggestion::factory()->create(['contact_email' => 'siti@example.com', 'name' => 'Print Baru']);

    $saved->update(['status' => SuggestionStatus::Accepted, 'admin_note' => 'Sudah kami tambahkan']);

    Mail::assertSent(SuggestionReviewed::class, function (SuggestionReviewed $mail) {
        $mail->assertHasSubject('Usulan Print Baru diterima');
        $mail->assertSeeInHtml('Usulanmu diterima');
        $mail->assertSeeInHtml('Sudah kami tambahkan');

        return $mail->hasTo('siti@example.com');
    });
});

test('the visitor is told when an admin rejects the suggestion, with the reason', function () {
    $saved = ShopSuggestion::factory()->create(['contact_email' => 'siti@example.com']);

    $saved->update(['status' => SuggestionStatus::Rejected, 'admin_note' => 'Alamatnya di luar area CitraLand']);

    Mail::assertSent(SuggestionReviewed::class, function (SuggestionReviewed $mail) {
        $mail->assertSeeInHtml('belum bisa kami tambahkan');
        $mail->assertSeeInHtml('Alamatnya di luar area CitraLand');

        return true;
    });
});

test('no decision email goes out while the suggestion is still pending or when only the note changes', function () {
    $saved = ShopSuggestion::factory()->create(['contact_email' => 'siti@example.com']);

    $saved->update(['admin_note' => 'Sedang dicek']);

    $saved->update(['status' => SuggestionStatus::Accepted]);
    $saved->update(['admin_note' => 'Catatan tambahan']);

    Mail::assertSent(SuggestionReviewed::class, 1);
});

test('a decision email failure does not stop the admin from saving', function () {
    $saved = ShopSuggestion::factory()->create(['contact_email' => 'siti@example.com']);
    Mail::shouldReceive('to')->andThrow(new RuntimeException('SMTP mati'));

    $saved->update(['status' => SuggestionStatus::Rejected]);

    expect($saved->refresh()->status)->toBe(SuggestionStatus::Rejected);
});

test('an admin changing the status in the panel emails the visitor', function () {
    $this->actingAs(User::factory()->admin()->create());
    $saved = ShopSuggestion::factory()->create(['contact_email' => 'siti@example.com']);

    Livewire::test(EditShopSuggestion::class, ['record' => $saved->getRouteKey()])
        ->fillForm(['status' => SuggestionStatus::Accepted->value])
        ->call('save');

    Mail::assertSent(SuggestionReviewed::class, fn (SuggestionReviewed $mail) => $mail->hasTo('siti@example.com'));
});
