<?php

use Illuminate\Support\Facades\Http;

use function Pest\Laravel\get;
use function Pest\Laravel\postJson;

beforeEach(function () {
    Http::preventStrayRequests();
});

function fakeWeatherResponse(): array {
    return [
        'name' => 'Nashville',
        'main' => [
            'temp' => 72.5,
            'feels_like' => 70.1,
            'humidity' => 55,
        ],
        'weather' => [
            [
                'main' => 'Clouds',
                'description' => 'scattered clouds',
                'icon' => '03d',
            ],
        ],
        'wind' => [
            'speed' => 8.3,
        ],
    ];
}

it('returns weather data for valid coordinates', function () {
    Http::fake([
        'api.openweathermap.org/*' => Http::response(fakeWeatherResponse()),
    ]);

    postJson('/weather', ['latitude' => 36.16, 'longitude' => -86.78])
        ->assertSuccessful()
        ->assertJsonStructure([
            'data' => ['city', 'temperature', 'feels_like', 'condition', 'description', 'icon', 'humidity', 'wind_speed', 'updated_at'],
        ])
        ->assertJsonPath('data.city', 'Nashville')
        ->assertJsonPath('data.temperature', 73)
        ->assertJsonPath('data.condition', 'Clouds');
});

it('requires latitude', function () {
    postJson('/weather', ['longitude' => -86.78])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['latitude']);
});

it('requires longitude', function () {
    postJson('/weather', ['latitude' => 36.16])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['longitude']);
});

it('rejects latitude out of range', function (float $latitude) {
    postJson('/weather', ['latitude' => $latitude, 'longitude' => -86.78])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['latitude']);
})->with([
    'too low' => -91.0,
    'too high' => 91.0,
]);

it('rejects longitude out of range', function (float $longitude) {
    postJson('/weather', ['latitude' => 36.16, 'longitude' => $longitude])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['longitude']);
})->with([
    'too low' => -181.0,
    'too high' => 181.0,
]);

it('rejects non-numeric coordinates', function () {
    postJson('/weather', ['latitude' => 'abc', 'longitude' => 'xyz'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['latitude', 'longitude']);
});

it('returns 503 when the weather API is unavailable', function () {
    Http::fake([
        'api.openweathermap.org/*' => Http::response('Service Unavailable', 503),
    ]);

    postJson('/weather', ['latitude' => 36.16, 'longitude' => -86.78])
        ->assertServiceUnavailable()
        ->assertJsonPath('message', 'The weather service returned an error. Please try again later.');
});

it('returns 503 when the weather API connection fails', function () {
    Http::fake([
        'api.openweathermap.org/*' => Http::failedConnection(),
    ]);

    postJson('/weather', ['latitude' => 36.16, 'longitude' => -86.78])
        ->assertServiceUnavailable()
        ->assertJsonPath('message', 'Unable to connect to the weather service. Please try again later.');
});

it('loads the homepage with the get weather button', function () {
    get('/')
        ->assertSuccessful()
        ->assertSee('Get My Weather');
});
