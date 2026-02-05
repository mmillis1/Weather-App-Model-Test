<?php

namespace App\Actions;

use App\Exceptions\WeatherServiceException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;

class FetchWeatherData {
    public function execute(float $lat, float $lon): array {
        $apiKey = config('services.openweathermap.api_key');
        $baseUrl = config('services.openweathermap.base_url');

        if (!$apiKey) {
            throw WeatherServiceException::apiError(500, 'Weather service is not configured.');
        }

        try {
            $response = \Http::timeout(10)
                ->retry(2, 100)
                ->get("{$baseUrl}/weather", [
                    'lat' => $lat,
                    'lon' => $lon,
                    'appid' => $apiKey,
                    'units' => 'metric',
                ]);

            if ($response->status() === 429) {
                throw WeatherServiceException::rateLimitExceeded();
            }

            if ($response->failed()) {
                throw WeatherServiceException::apiError(
                    $response->status(),
                    $response->json('message')
                );
            }

            return $this->transformResponse($response->json());
        } catch (ConnectionException $e) {
            throw WeatherServiceException::timeout();
        } catch (RequestException $e) {
            if ($e->response->status() === 429) {
                throw WeatherServiceException::rateLimitExceeded();
            }
            throw WeatherServiceException::apiError($e->response->status());
        }
    }

    private function transformResponse(array $data): array {
        if (!isset($data['name'], $data['main'], $data['weather'][0], $data['wind'])) {
            throw WeatherServiceException::invalidResponse();
        }

        return [
            'city' => $data['name'],
            'country' => $data['sys']['country'] ?? '',
            'temperature' => round($data['main']['temp'], 1),
            'feels_like' => round($data['main']['feels_like'], 1),
            'condition' => $data['weather'][0]['main'],
            'description' => $data['weather'][0]['description'],
            'icon' => $data['weather'][0]['icon'],
            'wind_speed' => round($data['wind']['speed'], 1),
            'humidity' => $data['main']['humidity'],
            'timestamp' => $data['dt'],
        ];
    }
}
