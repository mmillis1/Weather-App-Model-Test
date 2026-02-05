<?php

use function Pest\Laravel\get;

test('it renders the tempest homepage with weather call to action', function () {
    get('/')
        ->assertOk()
        ->assertSee('Tempest')
        ->assertSee('Get my weather')
        ->assertSee('Tempest needs JavaScript to request your location', false);
});
