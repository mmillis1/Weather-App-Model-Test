<?php

namespace App\Actions\Weather;

use App\Exceptions\WeatherProviderException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class GetCurrentWeather {
    /**
     * @return array<string, mixed>
     */
    public function execute(float $latitude, float $longitude, string $units): array {
        try {
            $response = Http::baseUrl((string) config('services.openweather.base_url'))
                ->acceptJson()
                ->timeout((int) config('services.openweather.timeout', 8))
                ->connectTimeout(4)
                ->retry(2, 150, throw: false)
                ->get('/data/2.5/weather', [
                    'lat' => $latitude,
                    'lon' => $longitude,
                    'units' => $units,
                    'appid' => config('services.openweather.key'),
                ]);

            /** @var Response $response */
            if ($response->status() === 429) {
                throw WeatherProviderException::rateLimited();
            }

            if (!$response->successful()) {
                throw WeatherProviderException::upstream();
            }

            /** @var array<string, mixed> $payload */
            $payload = $response->json();

            return $payload;
        } catch (ConnectionException) {
            throw WeatherProviderException::connection();
        }
    }
}
