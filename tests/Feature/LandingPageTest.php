<?php

it('has the correct title', function () {
    $response = $this->followingRedirects()->get('/'));
    $response->assertSee(config('app.name').'</title>', false);
});

it('redirects guests to the registration page', function () {});

it('redirects users to the catalog page', function () {});
