<?php

use App\Actions\FetchWeatherData;
use App\Exceptions\WeatherServiceException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

uses(Tests\TestCase::class);

test('successfully parses api response', function () {
    Http::fake([
        'api.openweathermap.org/*' => Http::response([
            'name' => 'Paris',
            'sys' => ['country' => 'FR'],
            'main' => [
                'temp' => 18.3,
                'feels_like' => 17.1,
                'humidity' => 65,
            ],
            'weather' => [
                ['main' => 'Clear', 'description' => 'clear sky', 'icon' => '01d'],
            ],
            'wind' => ['speed' => 2.5],
            'dt' => 1234567890,
        ], 200),
    ]);

    $action = new FetchWeatherData;
    $result = $action->execute(48.8566, 2.3522);

    expect($result)->toMatchArray([
        'city' => 'Paris',
        'country' => 'FR',
        'temperature' => 18.3,
        'feels_like' => 17.1,
        'condition' => 'Clear',
        'description' => 'clear sky',
        'icon' => '01d',
        'wind_speed' => 2.5,
        'humidity' => 65,
        'timestamp' => 1234567890,
    ]);
});

test('handles api timeout', function () {
    Http::fake(function () {
        throw new ConnectionException('Connection timeout');
    });

    $action = new FetchWeatherData;

    expect(fn () => $action->execute(48.8566, 2.3522))
        ->toThrow(WeatherServiceException::class, 'Weather service is taking too long to respond');
});

test('handles api 4xx error', function () {
    Http::fake([
        'api.openweathermap.org/*' => Http::response([
            'message' => 'Invalid coordinates',
        ], 400),
    ]);

    $action = new FetchWeatherData;

    expect(fn () => $action->execute(48.8566, 2.3522))
        ->toThrow(WeatherServiceException::class);
});

test('handles api 5xx error', function () {
    Http::fake([
        'api.openweathermap.org/*' => Http::response([], 500),
    ]);

    $action = new FetchWeatherData;

    expect(fn () => $action->execute(48.8566, 2.3522))
        ->toThrow(WeatherServiceException::class);
});

test('handles malformed api response', function () {
    Http::fake([
        'api.openweathermap.org/*' => Http::response([
            'invalid' => 'response',
        ], 200),
    ]);

    $action = new FetchWeatherData;

    expect(fn () => $action->execute(48.8566, 2.3522))
        ->toThrow(WeatherServiceException::class, 'Received invalid data from weather service');
});
