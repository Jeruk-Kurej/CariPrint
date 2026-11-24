<?php

use Inertia\Testing\AssertableInertia as Assert;

test('the privacy policy is public', function () {
    $this->get('/privasi')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('info/privacy'));
});
