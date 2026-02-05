<?php

namespace App\Http\Controllers;

use App\Actions\Weather\FetchWeather;
use App\Exceptions\Weather\ApiRateLimitException;
use App\Exceptions\Weather\ApiUnavailableException;
use App\Http\Requests\GetWeatherRequest;
use Illuminate\Http\JsonResponse;

class WeatherController extends Controller {
    public function __invoke(GetWeatherRequest $request, FetchWeather $fetchWeather): JsonResponse {
        try {
            $data = $fetchWeather->execute(
                (float) $request->validated('lat'),
                (float) $request->validated('lon'),
            );

            return response()->json(['data' => $data]);
        } catch (ApiRateLimitException) {
            return response()->json([
                'error' => 'Too many requests. Please wait a moment and try again.',
            ], 429);
        } catch (ApiUnavailableException) {
            return response()->json([
                'error' => 'Weather service is temporarily unavailable. Please try again later.',
            ], 503);
        }
    }
}
