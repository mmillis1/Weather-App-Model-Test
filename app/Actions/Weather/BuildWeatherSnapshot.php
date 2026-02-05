<?php

namespace App\Actions\Weather;

use Illuminate\Support\Carbon;

class BuildWeatherSnapshot {
    /**
     * @param  array<string, mixed>  $weatherPayload
     * @param  array{city: string|null, region: string|null, country: string|null, label: string|null}  $locationLabel
     * @return array{location: array{city: string|null, region: string|null, country: string|null, label: string|null}, weather: array{temperature: float|null, feels_like: float|null, condition: string, condition_code: string|null, humidity: int|null, wind_speed: float|null, units: string}, updated_at: string}
     */
    public function execute(array $weatherPayload, array $locationLabel, string $units): array {
        $fallbackCity = data_get($weatherPayload, 'name');
        $fallbackCountry = data_get($weatherPayload, 'sys.country');

        $city = is_string($locationLabel['city']) ? $locationLabel['city'] : (is_string($fallbackCity) ? $fallbackCity : null);
        $region = is_string($locationLabel['region']) ? $locationLabel['region'] : null;
        $country = is_string($locationLabel['country']) ? $locationLabel['country'] : (is_string($fallbackCountry) ? $fallbackCountry : null);

        $label = is_string($locationLabel['label']) ? $locationLabel['label'] : collect([$city, $country])->filter()->implode(', ');
        $label = $label !== '' ? $label : null;

        $temperature = data_get($weatherPayload, 'main.temp');
        $feelsLike = data_get($weatherPayload, 'main.feels_like');
        $humidity = data_get($weatherPayload, 'main.humidity');
        $windSpeed = data_get($weatherPayload, 'wind.speed');
        $condition = data_get($weatherPayload, 'weather.0.main');
        $conditionCode = data_get($weatherPayload, 'weather.0.icon');
        $updatedAtTimestamp = data_get($weatherPayload, 'dt');

        $updatedAt = is_numeric($updatedAtTimestamp)
            ? Carbon::createFromTimestampUTC((int) $updatedAtTimestamp)->toIso8601String()
            : now()->toIso8601String();

        return [
            'location' => [
                'city' => $city,
                'region' => $region,
                'country' => $country,
                'label' => $label,
            ],
            'weather' => [
                'temperature' => is_numeric($temperature) ? round((float) $temperature, 1) : null,
                'feels_like' => is_numeric($feelsLike) ? round((float) $feelsLike, 1) : null,
                'condition' => is_string($condition) ? $condition : 'Unknown',
                'condition_code' => is_string($conditionCode) ? $conditionCode : null,
                'humidity' => is_numeric($humidity) ? (int) $humidity : null,
                'wind_speed' => is_numeric($windSpeed) ? round((float) $windSpeed, 1) : null,
                'units' => $units,
            ],
            'updated_at' => $updatedAt,
        ];
    }
}
