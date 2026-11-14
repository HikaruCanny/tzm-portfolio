<?php

test('missing pages render the portfolio error screen', function () {
    $this->get('/this-page-does-not-exist')
        ->assertNotFound()
        ->assertInertia(fn ($page) => $page
            ->component('Error')
            ->where('status', 404)
        );
});
