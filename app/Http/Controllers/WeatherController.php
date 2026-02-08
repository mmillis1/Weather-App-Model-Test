<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\WeatherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WeatherController extends Controller {
    public function __construct(
        private WeatherService $weatherService
    ) {}

    public function index(): View {
        return view('weather');
    }

    public function getWeather(Request $request): JsonResponse {
        $validated = $request->validate([
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lon' => ['required', 'numeric', 'between:-180,180'],
        ], [
            'lat.required' => 'Latitude is required.',
            'lat.numeric' => 'Latitude must be a number.',
            'lat.between' => 'Latitude must be between -90 and 90.',
            'lon.required' => 'Longitude is required.',
            'lon.numeric' => 'Longitude must be a number.',
            'lon.between' => 'Longitude must be between -180 and 180.',
        ]);

        $weather = $this->weatherService->getWeatherByCoordinates(
            (float) $validated['lat'],
            (float) $validated['lon']
        );

        if ($weather === null) {
            return response()->json([
                'error' => 'Unable to fetch weather data. Please try again later.',
            ], 503);
        }

        return response()->json($weather);
    }
}
