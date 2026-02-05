<?php

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

use function Pest\Laravel\postJson;

beforeEach(function () {
    config()->set('services.openweather.base_url', 'https://api.openweathermap.org');
    config()->set('services.openweather.key', 'test-key');
    config()->set('services.openweather.timeout', 8);
});

test('it returns normalized weather payload for valid coordinates', function () {
    Http::fake([
        'https://api.openweathermap.org/data/2.5/weather*' => Http::response([
            'name' => 'San Francisco',
            'dt' => 1_770_330_190,
            'sys' => ['country' => 'US'],
            'main' => [
                'temp' => 18.4,
                'feels_like' => 17.8,
                'humidity' => 72,
            ],
            'weather' => [['main' => 'Clouds', 'icon' => '02d']],
            'wind' => ['speed' => 5.1],
        ], 200),
        'https://api.openweathermap.org/geo/1.0/reverse*' => Http::response([
            ['name' => 'San Francisco', 'state' => 'California', 'country' => 'US'],
        ], 200),
    ]);

    $response = postJson('/api/weather/current', [
        'latitude' => 37.7749,
        'longitude' => -122.4194,
        'units' => 'metric',
    ]);

    $response
        ->assertOk()
        ->assertJsonPath('meta.source', 'openweather')
        ->assertJsonPath('data.location.label', 'San Francisco, California')
        ->assertJsonPath('data.weather.condition', 'Clouds')
        ->assertJsonPath('data.weather.units', 'metric');

    Http::assertSentCount(2);
    Http::assertSent(function ($request): bool {
        return str_contains($request->url(), '/data/2.5/weather')
            && $request['lat'] === 37.7749
            && $request['lon'] === -122.4194
            && $request['units'] === 'metric'
            && $request['appid'] === 'test-key';
    });
});

test('it validates missing and invalid input', function (array $payload, array|string $errors) {
    $response = postJson('/api/weather/current', $payload);

    $response->assertUnprocessable()->assertInvalid($errors);
})->with([
    'missing latitude' => [
        ['longitude' => 10, 'units' => 'metric'],
        ['latitude'],
    ],
    'invalid latitude range' => [
        ['latitude' => 123, 'longitude' => 10, 'units' => 'metric'],
        ['latitude'],
    ],
    'invalid longitude range' => [
        ['latitude' => 10, 'longitude' => 200, 'units' => 'metric'],
        ['longitude'],
    ],
    'invalid units' => [
        ['latitude' => 10, 'longitude' => 20, 'units' => 'kelvin'],
        ['units'],
    ],
]);

test('it maps provider rate limit to 429', function () {
    Http::fake([
        'https://api.openweathermap.org/data/2.5/weather*' => Http::response([], 429),
    ]);

    $response = postJson('/api/weather/current', [
        'latitude' => 40,
        'longitude' => -70,
        'units' => 'metric',
    ]);

    $response
        ->assertStatus(429)
        ->assertJsonPath('error.code', 'weather_rate_limited');
});

test('it maps provider server failures to 502', function () {
    Http::fake([
        'https://api.openweathermap.org/data/2.5/weather*' => Http::response([], 500),
    ]);

    $response = postJson('/api/weather/current', [
        'latitude' => 40,
        'longitude' => -70,
        'units' => 'metric',
    ]);

    $response
        ->assertStatus(502)
        ->assertJsonPath('error.code', 'weather_upstream_error');
});

test('it maps connection problems to 503', function () {
    Http::fake([
        'https://api.openweathermap.org/data/2.5/weather*' => function () {
            throw new ConnectionException('Connection timeout.');
        },
    ]);

    $response = postJson('/api/weather/current', [
        'latitude' => 40,
        'longitude' => -70,
        'units' => 'metric',
    ]);

    $response
        ->assertStatus(503)
        ->assertJsonPath('error.code', 'weather_connection_error');
});
