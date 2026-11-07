<?php

test('public registration page is not available', function () {
    $this->get('/register')->assertNotFound();
});

test('public registration endpoint rejects new accounts', function () {
    $this->post('/register', [
        'name' => 'Intruder',
        'email' => 'intruder@example.test',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertNotFound();

    $this->assertDatabaseMissing('users', ['email' => 'intruder@example.test']);
});
