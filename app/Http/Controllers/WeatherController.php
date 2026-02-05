<?php

namespace App\Http\Controllers;

use App\Actions\Weather\GetCurrentWeather;
use App\Http\Requests\GetWeatherRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

class WeatherController extends Controller
{
    public function __invoke(GetWeatherRequest $request, GetCurrentWeather $getCurrentWeather): JsonResponse
    {
        $validated = $request->validated();
        $latitude = (float) $validated['lat'];
        $longitude = (float) $validated['lon'];

        $result = $getCurrentWeather->execute($latitude, $longitude);

        if (array_key_exists('error', $result)) {
            return Response::json([
                'message' => $result['error']['message'],
            ], $result['error']['status']);
        }

        return Response::json($result);
    }
}
