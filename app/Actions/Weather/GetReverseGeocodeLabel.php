<?php

namespace App\Actions\Weather;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class GetReverseGeocodeLabel {
    /**
     * @return array{city: string|null, region: string|null, country: string|null, label: string|null}
     */
    public function execute(float $latitude, float $longitude): array {
        try {
            $response = Http::baseUrl((string) config('services.openweather.base_url'))
                ->acceptJson()
                ->timeout((int) config('services.openweather.timeout', 8))
                ->connectTimeout(4)
                ->retry(1, 100, throw: false)
                ->get('/geo/1.0/reverse', [
                    'lat' => $latitude,
                    'lon' => $longitude,
                    'limit' => 1,
                    'appid' => config('services.openweather.key'),
                ]);

            /** @var Response $response */
            if (!$response->successful()) {
                return $this->emptyLocation();
            }

            /** @var array<int, array<string, mixed>> $payload */
            $payload = $response->json();

            if ($payload === []) {
                return $this->emptyLocation();
            }

            $first = $payload[0];
            $city = $first['name'] ?? null;
            $region = $first['state'] ?? null;
            $country = $first['country'] ?? null;

            $label = collect([$city, $region])->filter()->implode(', ');
            $label = $label !== '' ? $label : null;

            return [
                'city' => is_string($city) ? $city : null,
                'region' => is_string($region) ? $region : null,
                'country' => is_string($country) ? $country : null,
                'label' => $label,
            ];
        } catch (ConnectionException) {
            return $this->emptyLocation();
        }
    }

    /**
     * @return array{city: null, region: null, country: null, label: null}
     */
    private function emptyLocation(): array {
        return [
            'city' => null,
            'region' => null,
            'country' => null,
            'label' => null,
        ];
    }
}
