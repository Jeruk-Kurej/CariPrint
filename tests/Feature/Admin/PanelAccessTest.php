<?php

use App\Models\User;

test('guests are sent to the admin login page', function () {
    $this->get('/admin')->assertRedirect('/admin/login');
});

test('admins can open the admin panel', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->get('/admin')
        ->assertOk();
});

test('regular users are forbidden from the admin panel', function () {
    $this->actingAs(User::factory()->create())
        ->get('/admin')
        ->assertForbidden();
});

test('the admin panel offers a way back to the public website and the account page', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->get('/admin')
        ->assertOk()
        ->assertSee('Lihat website')
        ->assertSee('Akunku');
});
