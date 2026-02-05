<?php

use function Pest\Laravel\postJson;

test('successfully fetches weather data', function () {
    \Http::fake([
        'api.openweathermap.org/*' => \Http::response([
            'name' => 'London',
            'sys' => ['country' => 'GB'],
            'main' => [
                'temp' => 15.5,
                'feels_like' => 14.2,
                'humidity' => 72,
            ],
            'weather' => [
                ['main' => 'Clouds', 'description' => 'overcast clouds', 'icon' => '04d'],
            ],
            'wind' => ['speed' => 3.5],
            'dt' => 1234567890,
        ], 200),
    ]);

    $response = postJson('/weather', [
        'lat' => 51.5074,
        'lon' => -0.1278,
    ]);

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'data' => [
                'city' => 'London',
                'country' => 'GB',
                'temperature' => 15.5,
                'feels_like' => 14.2,
                'condition' => 'Clouds',
                'description' => 'overcast clouds',
                'wind_speed' => 3.5,
                'humidity' => 72,
            ],
        ]);
});

test('validates latitude is required', function () {
    $response = postJson('/weather', [
        'lon' => -0.1278,
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['lat']);
});

test('validates longitude is required', function () {
    $response = postJson('/weather', [
        'lat' => 51.5074,
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['lon']);
});

test('validates latitude range', function () {
    $response = postJson('/weather', [
        'lat' => 91,
        'lon' => -0.1278,
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['lat']);

    $response = postJson('/weather', [
        'lat' => -91,
        'lon' => -0.1278,
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['lat']);
});

test('validates longitude range', function () {
    $response = postJson('/weather', [
        'lat' => 51.5074,
        'lon' => 181,
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['lon']);

    $response = postJson('/weather', [
        'lat' => 51.5074,
        'lon' => -181,
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['lon']);
});

test('handles openweathermap api failure', function () {
    \Http::fake([
        'api.openweathermap.org/*' => \Http::response([], 500),
    ]);

    $response = postJson('/weather', [
        'lat' => 51.5074,
        'lon' => -0.1278,
    ]);

    $response->assertStatus(503)
        ->assertJson([
            'success' => false,
        ]);
});

test('handles openweathermap rate limit', function () {
    \Http::fake([
        'api.openweathermap.org/*' => \Http::response([
            'message' => 'Rate limit exceeded',
        ], 429),
    ]);

    $response = postJson('/weather', [
        'lat' => 51.5074,
        'lon' => -0.1278,
    ]);

    $response->assertStatus(429)
        ->assertJson([
            'success' => false,
            'error_code' => 'RATE_LIMIT',
        ]);
});
