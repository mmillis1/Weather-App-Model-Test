<?php

use Illuminate\Support\Facades\Http;

use function Pest\Laravel\getJson;

beforeEach(function () {
    config(['services.openweathermap.key' => 'test-api-key']);
    config(['services.openweathermap.base_url' => 'https://api.openweathermap.org']);
});

test('returns valid weather json for valid coordinates', function () {
    Http::fake([
        'api.openweathermap.org/*' => Http::response([
            'name' => 'Nashville',
            'sys' => ['country' => 'US'],
            'main' => ['temp' => 72, 'feels_like' => 70, 'humidity' => 55],
            'wind' => ['speed' => 8],
            'weather' => [['main' => 'Clear', 'description' => 'clear sky', 'icon' => '01d']],
        ]),
    ]);

    getJson('/api/weather?lat=36.16&lon=-86.78')
        ->assertSuccessful()
        ->assertJsonStructure([
            'data' => ['location', 'region', 'temperature', 'feels_like', 'humidity', 'wind_speed', 'condition', 'description', 'icon', 'updated_at'],
        ])
        ->assertJsonPath('data.location', 'Nashville');
});

test('validates lat is required', function () {
    getJson('/api/weather?lon=-86.78')
        ->assertUnprocessable()
        ->assertJsonValidationErrors('lat');
});

test('validates lon is required', function () {
    getJson('/api/weather?lat=36.16')
        ->assertUnprocessable()
        ->assertJsonValidationErrors('lon');
});

test('validates lat range', function () {
    getJson('/api/weather?lat=91&lon=0')
        ->assertUnprocessable()
        ->assertJsonValidationErrors('lat');
});

test('validates lon range', function () {
    getJson('/api/weather?lat=0&lon=181')
        ->assertUnprocessable()
        ->assertJsonValidationErrors('lon');
});

test('returns 429 on rate limit', function () {
    Http::fake([
        'api.openweathermap.org/*' => Http::response(['message' => 'rate limit'], 429),
    ]);

    getJson('/api/weather?lat=36.16&lon=-86.78')
        ->assertStatus(429)
        ->assertJsonPath('error', 'Too many requests. Please wait a moment and try again.');
});

test('returns 503 on api unavailable', function () {
    Http::fake([
        'api.openweathermap.org/*' => Http::response(['message' => 'error'], 500),
    ]);

    getJson('/api/weather?lat=36.16&lon=-86.78')
        ->assertServiceUnavailable()
        ->assertJsonPath('error', 'Weather service is temporarily unavailable. Please try again later.');
});

test('accepts float coordinates', function () {
    Http::fake([
        'api.openweathermap.org/*' => Http::response([
            'name' => 'Test',
            'sys' => ['country' => 'US'],
            'main' => ['temp' => 70, 'feels_like' => 68, 'humidity' => 50],
            'wind' => ['speed' => 5],
            'weather' => [['main' => 'Clear', 'description' => 'clear sky', 'icon' => '01d']],
        ]),
    ]);

    getJson('/api/weather?lat=36.1627&lon=-86.7816')
        ->assertSuccessful();
});
