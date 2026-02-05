<?php

use App\Actions\Weather\FetchWeather;
use App\Exceptions\Weather\ApiRateLimitException;
use App\Exceptions\Weather\ApiUnavailableException;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    config(['services.openweathermap.key' => 'test-api-key']);
    config(['services.openweathermap.base_url' => 'https://api.openweathermap.org']);
});

test('throws exception for invalid latitude', function () {
    $action = new FetchWeather;
    $action->execute(91.0, 0.0);
})->throws(InvalidArgumentException::class, 'Latitude must be between -90 and 90.');

test('throws exception for invalid longitude', function () {
    $action = new FetchWeather;
    $action->execute(0.0, 181.0);
})->throws(InvalidArgumentException::class, 'Longitude must be between -180 and 180.');

test('successfully fetches and parses weather data', function () {
    Http::fake([
        'api.openweathermap.org/*' => Http::response([
            'name' => 'Nashville',
            'sys' => ['country' => 'US'],
            'main' => ['temp' => 72.5, 'feels_like' => 70.1, 'humidity' => 55],
            'wind' => ['speed' => 8.3],
            'weather' => [
                ['main' => 'Clouds', 'description' => 'scattered clouds', 'icon' => '03d'],
            ],
        ]),
    ]);

    $action = new FetchWeather;
    $result = $action->execute(36.16, -86.78);

    expect($result)
        ->location->toBe('Nashville')
        ->region->toBe('US')
        ->temperature->toBe(73.0)
        ->feels_like->toBe(70.0)
        ->humidity->toBe(55)
        ->wind_speed->toBe(8.0)
        ->condition->toBe('Clouds')
        ->description->toBe('Scattered clouds')
        ->icon->toBe('03d');
});

test('throws ApiRateLimitException on 429 response', function () {
    Http::fake([
        'api.openweathermap.org/*' => Http::response(['message' => 'rate limit'], 429),
    ]);

    $action = new FetchWeather;
    $action->execute(36.16, -86.78);
})->throws(ApiRateLimitException::class);

test('throws ApiUnavailableException on 500 response', function () {
    Http::fake([
        'api.openweathermap.org/*' => Http::response(['message' => 'server error'], 500),
    ]);

    $action = new FetchWeather;
    $action->execute(36.16, -86.78);
})->throws(ApiUnavailableException::class);

test('sends correct query parameters', function () {
    Http::fake([
        'api.openweathermap.org/*' => Http::response([
            'name' => 'Test',
            'sys' => ['country' => 'US'],
            'main' => ['temp' => 70, 'feels_like' => 68, 'humidity' => 50],
            'wind' => ['speed' => 5],
            'weather' => [['main' => 'Clear', 'description' => 'clear sky', 'icon' => '01d']],
        ]),
    ]);

    $action = new FetchWeather;
    $action->execute(40.71, -74.01);

    Http::assertSent(function ($request) {
        return str_contains($request->url(), '/data/2.5/weather')
            && $request['lat'] == 40.71
            && $request['lon'] == -74.01
            && $request['appid'] === 'test-api-key'
            && $request['units'] === 'imperial';
    });
});

test('throws ApiUnavailableException on connection failure', function () {
    Http::fake([
        'api.openweathermap.org/*' => Http::failedConnection(),
    ]);

    $action = new FetchWeather;
    $action->execute(36.16, -86.78);
})->throws(ApiUnavailableException::class);
