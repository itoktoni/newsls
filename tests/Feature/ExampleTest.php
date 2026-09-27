<?php

it('redirects homepage to login when frontend is disabled', function () {
    // ponytail: DISABLE_FRONTEND=true → PublicController@index redirect ke login,
    // bukan render CMS homepage (200).
    $response = $this->get('/');

    $response->assertRedirect('/login');
});
