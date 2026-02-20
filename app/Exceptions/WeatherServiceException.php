<?php

namespace App\Exceptions;

use Exception;

class WeatherServiceException extends Exception {
    public function __construct(
        string $message = 'Unable to fetch weather data',
        public readonly int $statusCode = 503,
        public readonly ?string $errorCode = null
    ) {
        parent::__construct($message);
    }

    public static function timeout(): self {
        return new self(
            'Weather service is taking too long to respond. Please try again.',
            503,
            'TIMEOUT'
        );
    }

    public static function apiError(int $statusCode, ?string $message = null): self {
        return new self(
            $message ?? 'Unable to fetch weather data. Please try again later.',
            503,
            'API_ERROR'
        );
    }

    public static function rateLimitExceeded(): self {
        return new self(
            'Too many requests. Please try again in a few moments.',
            429,
            'RATE_LIMIT'
        );
    }

    public static function invalidResponse(): self {
        return new self(
            'Received invalid data from weather service. Please try again.',
            503,
            'INVALID_RESPONSE'
        );
    }
}
