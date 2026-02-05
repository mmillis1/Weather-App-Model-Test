<?php

namespace App\Actions\Weather;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class GetCurrentWeather {
    /**
     * @return array{city: string, temperature: float, feels_like: float, condition: string, description: string, icon: string, humidity: int, wind_speed: float, updated_at: string}
     *
     * @throws RuntimeException
     */
    public function execute(float $latitude, float $longitude): array {
        try {
            $response = Http::timeout(10)
                ->get(config('services.openweathermap.url') . '/weather', [
                    'lat' => $latitude,
                    'lon' => $longitude,
                    'appid' => config('services.openweathermap.key'),
                    'units' => 'imperial',
                ])
                ->throw();

            $data = $response->json();

            return [
                'city' => $data['name'],
                'temperature' => round($data['main']['temp']),
                'feels_like' => round($data['main']['feels_like']),
                'condition' => $data['weather'][0]['main'],
                'description' => ucfirst($data['weather'][0]['description']),
                'icon' => $data['weather'][0]['icon'],
                'humidity' => $data['main']['humidity'],
                'wind_speed' => round($data['wind']['speed']),
                'updated_at' => now()->format('g:i A'),
            ];
        } catch (ConnectionException $e) {
            throw new RuntimeException('Unable to connect to the weather service. Please try again later.', 503, $e);
        } catch (RequestException $e) {
            throw new RuntimeException('The weather service returned an error. Please try again later.', 503, $e);
        }
    }
}
