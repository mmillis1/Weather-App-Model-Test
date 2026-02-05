<?php

namespace App\Http\Controllers\Api\Weather;

use App\Actions\Weather\BuildWeatherSnapshot;
use App\Actions\Weather\GetCurrentWeather;
use App\Actions\Weather\GetReverseGeocodeLabel;
use App\Exceptions\WeatherProviderException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Weather\FetchWeatherRequest;
use Illuminate\Http\JsonResponse;

class ShowWeatherController extends Controller {
    public function __invoke(
        FetchWeatherRequest $request,
        GetCurrentWeather $getCurrentWeather,
        GetReverseGeocodeLabel $getReverseGeocodeLabel,
        BuildWeatherSnapshot $buildWeatherSnapshot,
    ): JsonResponse {
        $validated = $request->validated();

        try {
            $weatherPayload = $getCurrentWeather->execute(
                latitude: (float) $validated['latitude'],
                longitude: (float) $validated['longitude'],
                units: $validated['units'],
            );

            $locationLabel = $getReverseGeocodeLabel->execute(
                latitude: (float) $validated['latitude'],
                longitude: (float) $validated['longitude'],
            );

            return response()->json([
                'data' => $buildWeatherSnapshot->execute(
                    weatherPayload: $weatherPayload,
                    locationLabel: $locationLabel,
                    units: $validated['units'],
                ),
                'meta' => [
                    'source' => 'openweather',
                ],
            ]);
        } catch (WeatherProviderException $exception) {
            return response()->json([
                'error' => [
                    'code' => $exception->errorCode,
                    'message' => $exception->getMessage(),
                ],
            ], $exception->statusCode);
        }
    }
}
