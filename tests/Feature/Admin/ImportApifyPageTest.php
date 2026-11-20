<?php

use App\Enums\ShopStatus;
use App\Filament\Pages\ImportApify;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;

function apifyUpload(string $name = 'places.csv'): UploadedFile
{
    return UploadedFile::fake()->createWithContent($name, file_get_contents(base_path('tests/Fixtures/apify/places.csv')));
}

test('an admin imports an export as drafts and sees what happened', function () {
    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(ImportApify::class)
        ->fillForm(['file' => [apifyUpload()], 'radius' => 8])
        ->call('import')
        ->assertHasNoFormErrors()
        ->assertSee('4')
        ->assertSee('tanpa jam buka');

    expect(Shop::where('status', ShopStatus::Draft->value)->count())->toBe(4)
        ->and(Shop::published()->count())->toBe(0);
});

test('the radius is limited', function () {
    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(ImportApify::class)
        ->fillForm(['file' => [apifyUpload()], 'radius' => 500])
        ->call('import')
        ->assertHasFormErrors(['radius']);

    expect(Shop::count())->toBe(0);
});

test('a csv that is not an Apify export is refused with a clear message', function () {
    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(ImportApify::class)
        ->fillForm([
            'file' => [UploadedFile::fake()->createWithContent('lain.csv', "nama,umur\nBudi,20\n")],
            'radius' => 8,
        ])
        ->call('import')
        ->assertNotified('Berkas tidak bisa diimpor');

    expect(Shop::count())->toBe(0);
});

test('only admins can open the import page', function () {
    $this->get('/admin/import-apify')->assertRedirect('/admin/login');

    $this->actingAs(User::factory()->create())->get('/admin/import-apify')->assertForbidden();

    $this->actingAs(User::factory()->admin()->create())->get('/admin/import-apify')->assertOk()->assertSee('Impor dari Apify');
});

test('without a radius the whole city is accepted', function () {
    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(ImportApify::class)
        ->fillForm(['file' => [apifyUpload()]])
        ->call('import')
        ->assertHasNoFormErrors();

    expect(Shop::where('place_id', 'ChIJjauh')->exists())->toBeTrue()
        ->and(Shop::count())->toBe(5);
});

test('several files can be imported at once and a place found in both is added once', function () {
    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(ImportApify::class)
        ->fillForm(['file' => [apifyUpload('satu.csv'), apifyUpload('dua.csv')]])
        ->call('import')
        ->assertHasNoFormErrors();

    expect(Shop::count())->toBe(5);
});
