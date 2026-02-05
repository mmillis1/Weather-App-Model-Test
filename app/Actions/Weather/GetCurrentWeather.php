<?php

namespace App\Actions\Weather;

use Carbon\Carbon;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class GetCurrentWeather {
    public function execute(float $latitude, float $longitude): array {
        $apiKey = Config::get('services.openweather.key');

        if (!is_string($apiKey) || $apiKey === '') {
            return $this->errorResult('Weather service is not configured.', 500);
        }

        $cacheKey = $this->cacheKey($latitude, $longitude);

        return Cache::remember($cacheKey, Carbon::now()->addMinutes(4), function () use ($latitude, $longitude, $apiKey): array {
            /** @var Response $response */
            $response = Http::retry(2, 200)->timeout(6)->get('https://api.openweathermap.org/data/2.5/weather', [
                'lat' => $latitude,
                'lon' => $longitude,
                'appid' => $apiKey,
                'units' => 'imperial',
            ]);

            if (!$response->successful()) {
                return $this->errorFromResponse($response);
            }

            $payload = $response->json();

            if (!is_array($payload)) {
                return $this->errorResult('Weather service returned an unexpected response.', 502);
            }

            return $this->normalizeResponse($payload);
        });
    }

    private function cacheKey(float $latitude, float $longitude): string {
        $lat = number_format($latitude, 2, '.', '');
        $lon = number_format($longitude, 2, '.', '');

        return sprintf('weather:lat:%s:lon:%s:imperial', $lat, $lon);
    }

    private function normalizeResponse(array $payload): array {
        $temperature = $payload['main']['temp'] ?? null;
        $feelsLike = $payload['main']['feels_like'] ?? null;

        if (!is_numeric($temperature) || !is_numeric($feelsLike)) {
            return $this->errorResult('Weather service returned incomplete data.', 502);
        }

        $condition = $payload['weather'][0]['main'] ?? null;
        $windSpeed = $payload['wind']['speed'] ?? null;
        $humidity = $payload['main']['humidity'] ?? null;
        $updatedAt = $payload['dt'] ?? null;
        $timestamp = is_numeric($updatedAt) ? (int) $updatedAt : Carbon::now()->timestamp;

        return [
            'location' => [
                'name' => $payload['name'] ?? null,
                'region' => $payload['sys']['country'] ?? null,
            ],
            'weather' => [
                'temp' => (float) $temperature,
                'feels_like' => (float) $feelsLike,
                'condition' => is_string($condition) ? $condition : null,
                'wind_mph' => is_numeric($windSpeed) ? (float) $windSpeed : null,
                'humidity' => is_numeric($humidity) ? (int) $humidity : null,
            ],
            'updated_at' => Carbon::createFromTimestamp($timestamp)->utc()->toIso8601String(),
            'units' => [
                'temperature' => 'F',
                'wind' => 'mph',
            ],
        ];
    }

    private function errorFromResponse(Response $response): array {
        if ($response->tooManyRequests()) {
            return $this->errorResult('Weather service is temporarily rate limited. Please try again soon.', 429);
        }

        if ($response->serverError()) {
            return $this->errorResult('Weather service is having trouble right now.', 503);
        }

        if ($response->clientError()) {
            return $this->errorResult('Weather service rejected the request.', 502);
        }

        return $this->errorResult('Weather service returned an unexpected response.', 502);
    }

    private function errorResult(string $message, int $status): array {
        return [
            'error' => [
                'message' => $message,
                'status' => $status,
            ],
        ];
    }
}
