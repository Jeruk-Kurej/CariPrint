<?php

use App\Ai\Agents\ShopSearchAgent;
use Inertia\Testing\AssertableInertia as Assert;

/**
 * @param  array<string, mixed>  $overrides
 * @return array<string, mixed>
 */
function searchReading(array $overrides = []): array
{
    return [
        'area' => null, 'shop_name' => null, 'paper_size' => null, 'color_mode' => null,
        'services' => [], 'sort' => null, 'open_only' => false, 'max_km' => null,
        ...$overrides,
    ];
}

beforeEach(function () {
    config(['cariprint.ai.quota.guest' => 2]);
    /** JSON requests send no cookies without credentials, so every request would look like a new browser. */
    $this->withCredentials()->withCookie(config('session.cookie'), str_repeat('a', 40));
});

test('a sentence becomes the filters the directory understands', function () {
    ShopSearchAgent::fake([searchReading([
        'area' => '  Ngagel ', 'paper_size' => 'f4', 'services' => ['print'], 'sort' => 'price', 'open_only' => true, 'max_km' => 2,
    ])]);

    $this->postJson('/tempat/cari-ai', ['sentence' => 'print f4 termurah di sekitar ngagel'])
        ->assertOk()
        ->assertExactJson([
            'filters' => [
                'query' => 'Ngagel', 'openOnly' => true, 'sort' => 'price', 'paper' => 'f4', 'color' => 'any',
                'services' => ['print'], 'maxKm' => 2,
            ],
            'area' => 'Ngagel',
            'quota' => ['limit' => 2, 'remaining' => 1, 'isGuest' => true],
        ]);

    ShopSearchAgent::assertPrompted('print f4 termurah di sekitar ngagel');
});

test('answers outside the fixed choices are dropped', function () {
    ShopSearchAgent::fake([searchReading([
        'shop_name' => 'Alfa', 'paper_size' => 'b5', 'color_mode' => 'sepia',
        'services' => ['print', 'drop table shops'], 'sort' => 'rating', 'max_km' => 3,
    ])]);

    $this->postJson('/tempat/cari-ai', ['sentence' => 'alfa'])
        ->assertOk()
        ->assertJsonPath('filters', [
            'query' => 'Alfa', 'openOnly' => false, 'sort' => null, 'paper' => 'any', 'color' => 'any',
            'services' => ['print'], 'maxKm' => null,
        ]);
});

test('a sentence with nothing to filter on returns no filters but still counts', function () {
    ShopSearchAgent::fake([searchReading()]);

    $this->postJson('/tempat/cari-ai', ['sentence' => 'halo apa kabar'])
        ->assertOk()
        ->assertJsonPath('filters', null)
        ->assertJsonPath('quota.remaining', 1);
});

test('a failed AI call keeps the allowance and says to use the normal filters', function () {
    ShopSearchAgent::fake(fn () => throw new RuntimeException('provider down'));

    $this->postJson('/tempat/cari-ai', ['sentence' => 'print murah'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['sentence' => 'filter biasa']);

    $this->get('/tempat')->assertInertia(fn (Assert $page) => $page->where('quota.remaining', 2));
});

test('the AI is not called once the shared daily quota is used up', function () {
    $calls = 0;
    ShopSearchAgent::fake(function () use (&$calls) {
        $calls++;

        return searchReading(['sort' => 'price']);
    });

    $this->postJson('/tempat/cari-ai', ['sentence' => 'murah'])->assertOk();
    $this->postJson('/tempat/cari-ai', ['sentence' => 'murah'])->assertOk();
    $this->postJson('/tempat/cari-ai', ['sentence' => 'murah'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['sentence' => 'Jatah asisten']);

    expect($calls)->toBe(2);
});

test('an empty or too long sentence never reaches the AI', function (string $sentence) {
    ShopSearchAgent::fake()->preventStrayPrompts();

    $this->postJson('/tempat/cari-ai', ['sentence' => $sentence])->assertJsonValidationErrors(['sentence']);

    ShopSearchAgent::assertNeverPrompted();
})->with(['', str_repeat('a', 201)]);
