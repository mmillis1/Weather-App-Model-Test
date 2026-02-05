<?php

namespace App\Http\Controllers;

use App\Actions\Weather\GetCurrentWeather;
use App\Http\Requests\GetWeatherRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use RuntimeException;

class WeatherController extends Controller {
    public function index(): View {
        return view('weather');
    }

    public function show(GetWeatherRequest $request, GetCurrentWeather $getWeather): JsonResponse {
        try {
            $data = $getWeather->execute(
                (float) $request->validated('latitude'),
                (float) $request->validated('longitude'),
            );

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (RuntimeException) {
            return response()->json([
                'success' => false,
                'message' => 'Weather data is currently unavailable. Please try again later.',
            ], 503);
        }
    }
}
