<?php

use App\Actions\Weather\GetCurrentWeather;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    Http::preventStrayRequests();
});

it('returns correctly transformed weather data', function () {
    Http::fake([
        'api.openweathermap.org/*' => Http::response([
            'name' => 'Nashville',
            'main' => [
                'temp' => 72.5,
                'feels_like' => 70.1,
                'humidity' => 55,
            ],
            'weather' => [
                [
                    'main' => 'Clear',
                    'description' => 'clear sky',
                    'icon' => '01d',
                ],
            ],
            'wind' => [
                'speed' => 5.7,
            ],
        ]),
    ]);

    $action = new GetCurrentWeather;
    $result = $action->execute(36.16, -86.78);

    expect($result)
        ->city->toBe('Nashville')
        ->temperature->toEqual(73)
        ->feels_like->toEqual(70)
        ->condition->toBe('Clear')
        ->description->toBe('Clear sky')
        ->icon->toBe('01d')
        ->humidity->toBe(55)
        ->wind_speed->toEqual(6)
        ->updated_at->toBeString();
});

it('throws RuntimeException when the API returns an error', function () {
    Http::fake([
        'api.openweathermap.org/*' => Http::response('Internal Server Error', 500),
    ]);

    $action = new GetCurrentWeather;
    $action->execute(36.16, -86.78);
})->throws(RuntimeException::class, 'The weather service returned an error. Please try again later.');

it('throws RuntimeException when connection fails', function () {
    Http::fake([
        'api.openweathermap.org/*' => Http::failedConnection(),
    ]);

    $action = new GetCurrentWeather;
    $action->execute(36.16, -86.78);
})->throws(RuntimeException::class, 'Unable to connect to the weather service. Please try again later.');
