<?php

use Illuminate\Support\Facades\Http;

use function Pest\Laravel\get;
use function Pest\Laravel\getJson;

test('home page returns successful response with get weather button', function () {
    get('/')
        ->assertSuccessful()
        ->assertSee('Get My Weather');
});

test('valid coordinates return weather data', function () {
    Http::fake([
        'api.openweathermap.org/*' => Http::response([
            'name' => 'Nashville',
            'sys' => ['country' => 'US'],
            'main' => [
                'temp' => 72.5,
                'feels_like' => 70.1,
                'humidity' => 55,
            ],
            'weather' => [[
                'main' => 'Clear',
                'description' => 'clear sky',
                'icon' => '01d',
            ]],
            'wind' => ['speed' => 8.3],
        ]),
    ]);

    getJson(route('weather.show', ['latitude' => 36.16, 'longitude' => -86.78]))
        ->assertSuccessful()
        ->assertJson([
            'success' => true,
            'data' => [
                'city' => 'Nashville',
                'country' => 'US',
                'temperature' => 73,
                'feels_like' => 70,
                'condition' => 'Clear',
                'condition_description' => 'Clear sky',
                'icon' => '01d',
                'wind_speed' => 8,
                'humidity' => 55,
            ],
        ]);
});

test('latitude is required', function () {
    getJson(route('weather.show', ['longitude' => -86.78]))
        ->assertUnprocessable()
        ->assertJsonValidationErrors('latitude');
});

test('longitude is required', function () {
    getJson(route('weather.show', ['latitude' => 36.16]))
        ->assertUnprocessable()
        ->assertJsonValidationErrors('longitude');
});

test('latitude must be numeric', function () {
    getJson(route('weather.show', ['latitude' => 'abc', 'longitude' => -86.78]))
        ->assertUnprocessable()
        ->assertJsonValidationErrors('latitude');
});

test('longitude must be numeric', function () {
    getJson(route('weather.show', ['latitude' => 36.16, 'longitude' => 'abc']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors('longitude');
});

test('latitude must be between -90 and 90', function () {
    getJson(route('weather.show', ['latitude' => 91, 'longitude' => -86.78]))
        ->assertUnprocessable()
        ->assertJsonValidationErrors('latitude');
});

test('longitude must be between -180 and 180', function () {
    getJson(route('weather.show', ['latitude' => 36.16, 'longitude' => 181]))
        ->assertUnprocessable()
        ->assertJsonValidationErrors('longitude');
});

test('boundary values are accepted', function () {
    Http::fake([
        'api.openweathermap.org/*' => Http::response([
            'name' => 'North Pole',
            'sys' => ['country' => 'XX'],
            'main' => [
                'temp' => -40.0,
                'feels_like' => -55.0,
                'humidity' => 80,
            ],
            'weather' => [[
                'main' => 'Snow',
                'description' => 'heavy snow',
                'icon' => '13d',
            ]],
            'wind' => ['speed' => 25.0],
        ]),
    ]);

    getJson(route('weather.show', ['latitude' => 90, 'longitude' => 180]))
        ->assertSuccessful()
        ->assertJsonPath('success', true);

    getJson(route('weather.show', ['latitude' => -90, 'longitude' => -180]))
        ->assertSuccessful()
        ->assertJsonPath('success', true);
});

test('api failure returns 503 with friendly message', function () {
    Http::fake([
        'api.openweathermap.org/*' => Http::response(['message' => 'Invalid API key'], 401),
    ]);

    getJson(route('weather.show', ['latitude' => 36.16, 'longitude' => -86.78]))
        ->assertServiceUnavailable()
        ->assertJson([
            'success' => false,
            'message' => 'Weather data is currently unavailable. Please try again later.',
        ]);
});

test('connection failure returns 503', function () {
    Http::fake([
        'api.openweathermap.org/*' => fn () => throw new \Illuminate\Http\Client\ConnectionException('Connection refused'),
    ]);

    getJson(route('weather.show', ['latitude' => 36.16, 'longitude' => -86.78]))
        ->assertServiceUnavailable()
        ->assertJson([
            'success' => false,
        ]);
});
