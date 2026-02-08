<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeatherService {
    private string $apiKey;

    private string $baseUrl = 'https://api.openweathermap.org/data/2.5';

    public function __construct() {
        $this->apiKey = config('services.openweather.api_key', '');
    }

    /**
     * Fetch weather data for given coordinates.
     *
     * @return array<string, mixed>|null
     */
    public function getWeatherByCoordinates(float $latitude, float $longitude): ?array {
        if (empty($this->apiKey)) {
            Log::error('OpenWeather API key is not configured');

            return null;
        }

        try {
            $response = Http::timeout(10)->get("{$this->baseUrl}/weather", [
                'lat' => $latitude,
                'lon' => $longitude,
                'appid' => $this->apiKey,
                'units' => 'metric',
            ]);

            if ($response->failed()) {
                Log::warning('OpenWeather API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return null;
            }

            $data = $response->json();

            return $this->formatWeatherData($data);
        } catch (\Exception $e) {
            Log::error('Error fetching weather data', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Format raw API response into clean structure.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function formatWeatherData(array $data): array {
        return [
            'city' => $data['name'] ?? 'Unknown Location',
            'country' => $data['sys']['country'] ?? null,
            'temperature' => round($data['main']['temp'] ?? 0),
            'feels_like' => round($data['main']['feels_like'] ?? 0),
            'condition' => $data['weather'][0]['main'] ?? 'Unknown',
            'description' => $data['weather'][0]['description'] ?? '',
            'icon' => $data['weather'][0]['icon'] ?? '01d',
            'wind_speed' => $data['wind']['speed'] ?? 0,
            'humidity' => $data['main']['humidity'] ?? 0,
            'updated_at' => now()->format('g:i A'),
        ];
    }
}
