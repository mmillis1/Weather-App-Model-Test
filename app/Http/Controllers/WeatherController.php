<?php

namespace App\Http\Controllers;

use App\Actions\FetchWeatherData;
use App\Exceptions\WeatherServiceException;
use App\Http\Requests\WeatherRequest;
use Illuminate\Http\JsonResponse;

class WeatherController extends Controller {
    public function show(WeatherRequest $request, FetchWeatherData $fetchWeatherData): JsonResponse {
        try {
            $weatherData = $fetchWeatherData->execute(
                $request->validated('lat'),
                $request->validated('lon')
            );

            return response()->json([
                'success' => true,
                'data' => $weatherData,
            ]);
        } catch (WeatherServiceException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error_code' => $e->errorCode,
            ], $e->statusCode);
        }
    }
}
