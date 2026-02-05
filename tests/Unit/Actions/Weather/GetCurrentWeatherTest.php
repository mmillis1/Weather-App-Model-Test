<?php

use App\Actions\Weather\GetCurrentWeather;
use App\Exceptions\WeatherProviderException;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    config()->set('services.openweather.base_url', 'https://api.openweathermap.org');
    config()->set('services.openweather.key', 'unit-test-key');
    config()->set('services.openweather.timeout', 8);
});

test('it fetches and returns weather payload', function () {
    Http::fake([
        'https://api.openweathermap.org/data/2.5/weather*' => Http::response([
            'main' => ['temp' => 22.2],
            'weather' => [['main' => 'Clear']],
        ], 200),
    ]);

    $payload = (new GetCurrentWeather)->execute(32.7, -117.1, 'imperial');

    expect($payload['main']['temp'])->toBe(22.2)
        ->and($payload['weather'][0]['main'])->toBe('Clear');
});

test('it throws a domain exception for upstream failures', function () {
    Http::fake([
        'https://api.openweathermap.org/data/2.5/weather*' => Http::response([], 500),
    ]);

    expect(fn () => (new GetCurrentWeather)->execute(32.7, -117.1, 'metric'))
        ->toThrow(WeatherProviderException::class);
});

test('it throws a rate-limited domain exception for 429 responses', function () {
    Http::fake([
        'https://api.openweathermap.org/data/2.5/weather*' => Http::response([], 429),
    ]);

    try {
        (new GetCurrentWeather)->execute(32.7, -117.1, 'metric');
    } catch (WeatherProviderException $exception) {
        expect($exception->statusCode)->toBe(429)
            ->and($exception->errorCode)->toBe('weather_rate_limited');

        return;
    }

    expect()->fail('Expected WeatherProviderException was not thrown.');
});
