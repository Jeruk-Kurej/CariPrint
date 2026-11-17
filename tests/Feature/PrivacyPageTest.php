<?php

use Inertia\Testing\AssertableInertia as Assert;

test('the privacy policy is public', function () {
    $this->get('/privasi')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('info/privacy')->where('contactEmail', null));
});

test('the privacy policy shows the contact email when one is configured', function () {
    config(['cariprint.contact.email' => 'kontak@example.com']);

    $this->get('/privasi')->assertInertia(fn (Assert $page) => $page->where('contactEmail', 'kontak@example.com'));
});
