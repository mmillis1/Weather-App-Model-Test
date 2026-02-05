<?php

namespace App\Actions\Weather;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class GetCurrentWeather {
    /**
     * @return array{
     *     city: string,
     *     country: string,
     *     temperature: float,
     *     feels_like: float,
     *     condition: string,
     *     condition_description: string,
     *     icon: string,
     *     wind_speed: float,
     *     humidity: int,
     *     updated_at: string
     * }
     */
    public function execute(float $latitude, float $longitude): array {
        try {
            $response = Http::get(config('services.openweathermap.url') . '/weather', [
                'lat' => $latitude,
                'lon' => $longitude,
                'appid' => config('services.openweathermap.key'),
                'units' => 'imperial',
            ]);
        } catch (ConnectionException $e) {
            throw new RuntimeException('Unable to connect to weather service.', 0, $e);
        }

        if ($response->failed()) {
            throw new RuntimeException('Weather service returned an error: ' . $response->status());
        }

        $data = $response->json();

        return [
            'city' => $data['name'],
            'country' => $data['sys']['country'],
            'temperature' => round($data['main']['temp']),
            'feels_like' => round($data['main']['feels_like']),
            'condition' => $data['weather'][0]['main'],
            'condition_description' => ucfirst($data['weather'][0]['description']),
            'icon' => $data['weather'][0]['icon'],
            'wind_speed' => round($data['wind']['speed']),
            'humidity' => $data['main']['humidity'],
            'updated_at' => now()->format('g:i A'),
        ];
    }
}
