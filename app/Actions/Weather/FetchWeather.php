<?php

namespace App\Actions\Weather;

use App\Exceptions\Weather\ApiRateLimitException;
use App\Exceptions\Weather\ApiUnavailableException;
use Carbon\Carbon;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class FetchWeather {
    /**
     * Fetch weather data for the given coordinates.
     *
     * @return array{
     *     location: string,
     *     region: string,
     *     temperature: float,
     *     feels_like: float,
     *     humidity: int,
     *     wind_speed: float,
     *     condition: string,
     *     description: string,
     *     icon: string,
     *     updated_at: string,
     * }
     */
    public function execute(float $lat, float $lon): array {
        if ($lat < -90 || $lat > 90) {
            throw new \InvalidArgumentException('Latitude must be between -90 and 90.');
        }

        if ($lon < -180 || $lon > 180) {
            throw new \InvalidArgumentException('Longitude must be between -180 and 180.');
        }

        $baseUrl = config('services.openweathermap.base_url');
        $apiKey = config('services.openweathermap.key');

        try {
            $response = Http::timeout(10)->get("{$baseUrl}/data/2.5/weather", [
                'lat' => $lat,
                'lon' => $lon,
                'appid' => $apiKey,
                'units' => 'imperial',
            ]);
        } catch (ConnectionException $e) {
            throw new ApiUnavailableException('Unable to connect to weather service.', 0, $e);
        }

        if ($response->status() === 429) {
            throw new ApiRateLimitException('Weather API rate limit exceeded. Please try again later.');
        }

        if ($response->failed()) {
            throw new ApiUnavailableException('Weather service is currently unavailable.');
        }

        $data = $response->json();
        $weather = $data['weather'][0] ?? [];

        return [
            'location' => $data['name'] ?? 'Unknown',
            'region' => $data['sys']['country'] ?? '',
            'temperature' => round($data['main']['temp']),
            'feels_like' => round($data['main']['feels_like']),
            'humidity' => $data['main']['humidity'],
            'wind_speed' => round($data['wind']['speed']),
            'condition' => $weather['main'] ?? 'Unknown',
            'description' => ucfirst($weather['description'] ?? ''),
            'icon' => $weather['icon'] ?? '01d',
            'updated_at' => Carbon::now()->format('g:i A'),
        ];
    }
}
