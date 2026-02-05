<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetWeatherRequest;
use Illuminate\Support\Facades\Http;

class WeatherController extends Controller {
    public function __invoke(GetWeatherRequest $request) {
        $lat = $request->input('lat');
        $lon = $request->input('lon');

        $apiKey = config('weather.api_key');

        if (blank($apiKey)) {
            return response()->json([
                'error' => 'API key is not configured',
            ], 500);
        }

        try {
            $response = Http::get(config('weather.url'), [
                'lat' => $lat,
                'lon' => $lon,
                'appid' => $apiKey,
                'units' => 'metric',
            ]);

            if (!$response->successful()) {
                return response()->json([
                    'error' => $response->json('message', 'Failed to fetch weather data'),
                ], $response->status());
            }

            $data = $response->json();

            return response()->json([
                'city' => $data['name'] ?? null,
                'region' => $data['sys']['country'] ?? null,
                'temp' => $data['main']['temp'] ?? null,
                'feels_like' => $data['main']['feels_like'] ?? null,
                'condition' => $data['weather'][0]['main'] ?? null,
                'description' => $data['weather'][0]['description'] ?? null,
                'wind' => $data['wind']['speed'] ?? null,
                'humidity' => $data['main']['humidity'] ?? null,
                'updated_at' => now()->format('g:i A'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch weather data. Please try again.',
            ], 500);
        }
    }
}
