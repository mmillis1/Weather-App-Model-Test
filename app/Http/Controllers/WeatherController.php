<?php

namespace App\Http\Controllers;

use App\Actions\Weather\GetCurrentWeather;
use App\Http\Requests\GetWeatherRequest;
use Illuminate\Http\JsonResponse;
use RuntimeException;

class WeatherController extends Controller {
    public function __invoke(GetWeatherRequest $request, GetCurrentWeather $getCurrentWeather): JsonResponse {
        try {
            $weather = $getCurrentWeather->execute(
                (float) $request->validated('latitude'),
                (float) $request->validated('longitude'),
            );

            return response()->json(['data' => $weather]);
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 503);
        }
    }
}
