<?php

use App\Actions\Weather\BuildWeatherSnapshot;

test('it builds the expected weather snapshot shape', function () {
    $snapshot = (new BuildWeatherSnapshot)->execute(
        weatherPayload: [
            'name' => 'Berlin',
            'sys' => ['country' => 'DE'],
            'dt' => 1_770_330_190,
            'main' => [
                'temp' => 12.4,
                'feels_like' => 10.6,
                'humidity' => 81,
            ],
            'weather' => [['main' => 'Clear', 'icon' => '01d']],
            'wind' => ['speed' => 3.7],
        ],
        locationLabel: [
            'city' => 'Berlin',
            'region' => 'Berlin',
            'country' => 'DE',
            'label' => 'Berlin, Berlin',
        ],
        units: 'metric',
    );

    expect($snapshot)
        ->toHaveKeys(['location', 'weather', 'updated_at'])
        ->and($snapshot['location']['label'])->toBe('Berlin, Berlin')
        ->and($snapshot['weather']['temperature'])->toBe(12.4)
        ->and($snapshot['weather']['units'])->toBe('metric')
        ->and($snapshot['updated_at'])->toStartWith('2026-02');
});

test('it falls back when optional data is missing', function () {
    $snapshot = (new BuildWeatherSnapshot)->execute(
        weatherPayload: [
            'name' => 'Unknown City',
            'sys' => ['country' => 'US'],
            'main' => [],
            'weather' => [],
            'wind' => [],
        ],
        locationLabel: [
            'city' => null,
            'region' => null,
            'country' => null,
            'label' => null,
        ],
        units: 'imperial',
    );

    expect($snapshot['location']['label'])->toBe('Unknown City, US')
        ->and($snapshot['weather']['condition'])->toBe('Unknown')
        ->and($snapshot['weather']['temperature'])->toBeNull()
        ->and($snapshot['weather']['units'])->toBe('imperial');
});
