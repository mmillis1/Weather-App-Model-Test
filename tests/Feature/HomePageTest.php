<?php

use function Pest\Laravel\get;

test('home page returns 200', function () {
    get('/')->assertSuccessful();
});

test('contains nimbus text', function () {
    get('/')->assertSee('Nimbus');
});

test('contains weather card structure', function () {
    get('/')
        ->assertSee('weather-card', escape: false)
        ->assertSee('weather-location', escape: false)
        ->assertSee('weather-temp', escape: false);
});

test('contains noscript fallback', function () {
    get('/')
        ->assertSee('noscript', escape: false)
        ->assertSee('JavaScript is required');
});
