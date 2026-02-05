<?php

namespace App\Exceptions;

use Exception;

class WeatherProviderException extends Exception {
    public function __construct(
        public readonly int $statusCode,
        public readonly string $errorCode,
        string $message,
    ) {
        parent::__construct($message);
    }

    public static function rateLimited(): self {
        return new self(
            statusCode: 429,
            errorCode: 'weather_rate_limited',
            message: 'Weather service is temporarily rate limited. Please try again shortly.',
        );
    }

    public static function upstream(): self {
        return new self(
            statusCode: 502,
            errorCode: 'weather_upstream_error',
            message: 'Weather data is unavailable right now.',
        );
    }

    public static function connection(): self {
        return new self(
            statusCode: 503,
            errorCode: 'weather_connection_error',
            message: 'Unable to reach weather service right now.',
        );
    }
}
