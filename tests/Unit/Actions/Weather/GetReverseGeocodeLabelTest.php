<?php

use App\Actions\Weather\GetReverseGeocodeLabel;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    config()->set('services.openweather.base_url', 'https://api.openweathermap.org');
    config()->set('services.openweather.key', 'unit-test-key');
    config()->set('services.openweather.timeout', 8);
});

test('it formats an enriched location label', function () {
    Http::fake([
        'https://api.openweathermap.org/geo/1.0/reverse*' => Http::response([
            ['name' => 'Austin', 'state' => 'Texas', 'country' => 'US'],
        ], 200),
    ]);

    $location = (new GetReverseGeocodeLabel)->execute(30.26, -97.74);

    expect($location)
        ->toMatchArray([
            'city' => 'Austin',
            'region' => 'Texas',
            'country' => 'US',
            'label' => 'Austin, Texas',
        ]);
});

test('it falls back gracefully when region is missing', function () {
    Http::fake([
        'https://api.openweathermap.org/geo/1.0/reverse*' => Http::response([
            ['name' => 'Oslo', 'country' => 'NO'],
        ], 200),
    ]);

    $location = (new GetReverseGeocodeLabel)->execute(59.91, 10.75);

    expect($location)
        ->toMatchArray([
            'city' => 'Oslo',
            'region' => null,
            'country' => 'NO',
            'label' => 'Oslo',
        ]);
});

test('it returns empty values when reverse geocode fails', function () {
    Http::fake([
        'https://api.openweathermap.org/geo/1.0/reverse*' => Http::response([], 500),
    ]);

    $location = (new GetReverseGeocodeLabel)->execute(59.91, 10.75);

    expect($location)
        ->toMatchArray([
            'city' => null,
            'region' => null,
            'country' => null,
            'label' => null,
        ]);
});
