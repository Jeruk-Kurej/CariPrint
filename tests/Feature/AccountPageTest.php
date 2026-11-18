<?php

use App\Enums\SuggestionStatus;
use App\Models\ShopSuggestion;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('the account page needs a signed-in user', function () {
    $this->get('/akun')->assertRedirect('/login');
});

test('a user sees only the suggestions sent from their own email, newest first', function () {
    $user = User::factory()->create(['email' => 'siti@example.com']);
    ShopSuggestion::factory()->create(['contact_email' => 'siti@example.com', 'name' => 'Lama', 'created_at' => now()->subDay()]);
    ShopSuggestion::factory()->create(['contact_email' => 'siti@example.com', 'name' => 'Baru', 'status' => SuggestionStatus::Accepted, 'admin_note' => 'Sudah ditambahkan']);
    ShopSuggestion::factory()->create(['contact_email' => 'orang-lain@example.com', 'name' => 'Bukan milik Siti']);

    $this->actingAs($user)->get('/akun')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('account/show')
            ->has('suggestions', 2)
            ->where('suggestions.0.name', 'Baru')
            ->where('suggestions.0.status', 'accepted')
            ->where('suggestions.0.statusLabel', 'Diterima')
            ->where('suggestions.0.adminNote', 'Sudah ditambahkan')
            ->where('suggestions.1.name', 'Lama')
            ->where('isAdmin', false));
});

test('an admin is told on the account page so the admin panel link can show', function () {
    $this->actingAs(User::factory()->admin()->create())->get('/akun')
        ->assertInertia(fn (Assert $page) => $page->where('isAdmin', true));
});

test('a user with no suggestions gets an empty list', function () {
    $this->actingAs(User::factory()->create())->get('/akun')
        ->assertInertia(fn (Assert $page) => $page->has('suggestions', 0));
});
