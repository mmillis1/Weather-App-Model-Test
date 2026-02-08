<?php

declare(strict_types=1);

use App\Services\WeatherService;

use function Pest\Laravel\mock;
use function Pest\Laravel\postJson;

describe('Weather Page', function () {
    it('displays the weather page', function () {
        $this->get('/')
            ->assertSuccessful()
            ->assertViewIs('weather')
            ->assertSee('Get my weather');
    });

    it('includes progressive enhancement noscript tag', function () {
        $this->get('/')
            ->assertSuccessful()
            ->assertSee('JavaScript Required');
    });

    it('includes csrf token meta tag', function () {
        $this->get('/')
            ->assertSuccessful()
            ->assertSee('csrf-token');
    });
});

describe('Weather API', function () {
    it('returns weather data for valid coordinates', function () {
        $mockWeather = [
            'city' => 'London',
            'country' => 'GB',
            'temperature' => 18,
            'feels_like' => 16,
            'condition' => 'Clouds',
            'description' => 'overcast clouds',
            'icon' => '04d',
            'wind_speed' => 3.5,
            'humidity' => 72,
            'updated_at' => '2:30 PM',
        ];

        mock(WeatherService::class)
            ->shouldReceive('getWeatherByCoordinates')
            ->with(51.5074, -0.1278)
            ->once()
            ->andReturn($mockWeather);

        postJson('/api/weather', [
            'lat' => 51.5074,
            'lon' => -0.1278,
        ])
            ->assertSuccessful()
            ->assertJson($mockWeather);
    });

    it('returns error when weather service fails', function () {
        mock(WeatherService::class)
            ->shouldReceive('getWeatherByCoordinates')
            ->with(51.5074, -0.1278)
            ->once()
            ->andReturn(null);

        postJson('/api/weather', [
            'lat' => 51.5074,
            'lon' => -0.1278,
        ])
            ->assertStatus(503)
            ->assertJson([
                'error' => 'Unable to fetch weather data. Please try again later.',
            ]);
    });
});

describe('Weather API Validation', function () {
    it('validates latitude is required', function () {
        postJson('/api/weather', [
            'lon' => -0.1278,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['lat']);
    });

    it('validates longitude is required', function () {
        postJson('/api/weather', [
            'lat' => 51.5074,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['lon']);
    });

    it('validates latitude must be numeric', function () {
        postJson('/api/weather', [
            'lat' => 'not-a-number',
            'lon' => -0.1278,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['lat']);
    });

    it('validates longitude must be numeric', function () {
        postJson('/api/weather', [
            'lat' => 51.5074,
            'lon' => 'not-a-number',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['lon']);
    });

    it('validates latitude must be between -90 and 90', function () {
        postJson('/api/weather', [
            'lat' => 91,
            'lon' => -0.1278,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['lat']);

        postJson('/api/weather', [
            'lat' => -91,
            'lon' => -0.1278,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['lat']);
    });

    it('validates longitude must be between -180 and 180', function () {
        postJson('/api/weather', [
            'lat' => 51.5074,
            'lon' => 181,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['lon']);

        postJson('/api/weather', [
            'lat' => 51.5074,
            'lon' => -181,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['lon']);
    });
});
