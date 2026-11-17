<?php

use App\Enums\SuggestionStatus;
use App\Filament\Resources\ShopSuggestions\Pages\EditShopSuggestion;
use App\Filament\Resources\ShopSuggestions\Pages\ListShopSuggestions;
use App\Mail\NewShopSuggestion;
use App\Models\Shop;
use App\Models\ShopSuggestion;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Inertia\Testing\AssertableInertia as Assert;
use Livewire\Livewire;

beforeEach(function () {
    Mail::fake();
    config(['cariprint.admin.email' => 'admin@cariprint.test']);
});

function suggestion(array $overrides = []): array
{
    return ['name' => 'Print Baru', 'address' => 'Ruko G-Walk Blok A', ...$overrides];
}

test('the suggestion form is public', function () {
    $this->get('/usul-tempat')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('suggestions/create')->where('submitted', false));
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

test('the suggestion is kept when the email cannot be sent', function () {
    Mail::shouldReceive('to')->andThrow(new RuntimeException('SMTP mati'));

    $this->post('/usul-tempat', suggestion())->assertRedirect('/usul-tempat');

    expect(ShopSuggestion::count())->toBe(1);
});

test('no email is attempted when no admin address is configured', function () {
    config(['cariprint.admin.email' => null]);

    $this->post('/usul-tempat', suggestion());

    Mail::assertNothingSent();
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
    'a broken email' => [['name' => 'Print Baru', 'address' => 'Jalan A', 'contact_email' => 'bukan-email'], 'contact_email'],
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
