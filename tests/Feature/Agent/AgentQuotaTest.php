<?php

use App\Ai\Agents\PrintRequestAgent;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    Shop::factory()->create();
    config(['cariprint.ai.quota.guest' => 2, 'cariprint.ai.quota.user' => 4]);
    openBrowser($this);

    PrintRequestAgent::fake(fn () => [
        'paper_size' => 'a4', 'bw_pages' => 5, 'color_pages' => 0, 'copies' => 1, 'binding' => null,
    ]);
});

/**
 * The test client sends no cookies, so without this every request would look like a new browser.
 */
function openBrowser(object $test): void
{
    $test->withCookie(config('session.cookie'), Str::random(40));
}

function askAssistant(object $test): TestResponse
{
    return $test->from('/agent')->post('/agent', ['request' => 'print 5 halaman']);
}

test('a guest can ask only as many times as the guest quota allows', function () {
    askAssistant($this)->assertOk();
    askAssistant($this)->assertOk();

    askAssistant($this)->assertRedirect('/agent')->assertSessionHasErrors('request');

    expect(session('errors')->first('request'))
        ->toContain('2 permintaan per hari')
        ->toContain('Masuk dengan Google');
});

test('the AI is called once per accepted request and not at all after the quota is used up', function () {
    $calls = 0;
    PrintRequestAgent::fake(function () use (&$calls) {
        $calls++;

        return ['paper_size' => 'a4', 'bw_pages' => 5, 'color_pages' => 0, 'copies' => 1, 'binding' => null];
    });

    foreach (range(1, 4) as $attempt) {
        askAssistant($this);
    }

    expect($calls)->toBe(2);
});

test('a failed AI call does not use up the allowance', function () {
    PrintRequestAgent::fake(fn () => throw new RuntimeException('provider down'));

    askAssistant($this)->assertSessionHasErrors('request');
    askAssistant($this)->assertSessionHasErrors('request');
    askAssistant($this)->assertSessionHasErrors('request');

    expect(session('errors')->first('request'))->not->toContain('Jatah asisten');
});

test('an invalid request does not use up the allowance', function () {
    $this->postJson('/agent', ['request' => ''])->assertUnprocessable();
    $this->postJson('/agent', ['request' => ''])->assertUnprocessable();

    askAssistant($this)->assertOk();
    askAssistant($this)->assertOk();
});

test('a logged-in user has the larger quota and is told to wait until tomorrow', function () {
    $this->actingAs(User::factory()->create());

    foreach (range(1, 4) as $attempt) {
        askAssistant($this)->assertOk();
    }

    askAssistant($this)->assertSessionHasErrors('request');

    expect(session('errors')->first('request'))
        ->toContain('4 permintaan per hari')
        ->not->toContain('Masuk dengan Google');
});

test('users do not share a quota', function () {
    $this->actingAs(User::factory()->create());
    foreach (range(1, 4) as $attempt) {
        askAssistant($this);
    }

    $this->actingAs(User::factory()->create());

    askAssistant($this)->assertOk();
});

test('admins are not limited', function () {
    $this->actingAs(User::factory()->admin()->create());

    foreach (range(1, 8) as $attempt) {
        askAssistant($this)->assertOk();
    }

    $this->get('/agent')->assertInertia(fn (Assert $page) => $page
        ->where('quota.limit', null)
        ->where('quota.remaining', null));
});

test('the quota comes back the next day', function () {
    $this->travelTo(now('Asia/Jakarta')->setTime(10, 0));

    askAssistant($this);
    askAssistant($this);
    askAssistant($this)->assertSessionHasErrors('request');

    $this->travelTo(now('Asia/Jakarta')->addDay()->setTime(0, 5));

    askAssistant($this)->assertOk();
});

test('the page shows what is left of the quota', function () {
    $this->get('/agent')->assertInertia(fn (Assert $page) => $page
        ->where('quota', ['limit' => 2, 'remaining' => 2, 'isGuest' => true]));

    askAssistant($this);

    $this->get('/agent')->assertInertia(fn (Assert $page) => $page->where('quota.remaining', 1));
});

test('the IP ceiling stops a script that keeps changing its session', function () {
    config(['cariprint.ai.quota.ip_ceiling' => 3]);

    foreach (range(1, 3) as $attempt) {
        openBrowser($this);
        askAssistant($this)->assertOk();
    }

    openBrowser($this);

    askAssistant($this)->assertSessionHasErrors('request');
});
