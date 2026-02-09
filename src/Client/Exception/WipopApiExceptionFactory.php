<?php

declare(strict_types=1);

namespace Wipop\Client\Exception;

use Throwable;
use Wipop\Exception\WipopException;

final class WipopApiExceptionFactory
{
    /**
     * @param array<string, mixed> $payload
     */
    public static function fromPayload(array $payload, ?Throwable $previous = null): WipopException
    {
        $responseCode = isset($payload['response_code']) && is_array($payload['response_code'])
            ? $payload['response_code']
            : [];

        $code = isset($responseCode['code']) && is_string($responseCode['code'])
            ? $responseCode['code']
            : null;

        $defaultMessage = $payload['detail'] ?? ($payload['status'] ?? null);
        $message = isset($responseCode['message']) && is_string($responseCode['message'])
            ? $responseCode['message']
            : (is_string($defaultMessage) ? $defaultMessage : null);

        return self::fromCode($code, $message, $previous);
    }

    public static function fromCode(
        ?string $code,
        ?string $message = null,
        ?Throwable $previous = null
    ): WipopException {
        return match ($code) {
            ApiErrorCode::AC000 => new WipopApiUnexpectedException($message, $previous),
            ApiErrorCode::BC000 => new WipopApiBusinessException($message, $previous),
            ApiErrorCode::DC000 => new WipopApiDataAccessException($message, $previous),
            ApiErrorCode::VC000 => new WipopApiValidationException($message, $previous),
            ApiErrorCode::BC0001 => new WipopApiAuthenticationException($message, $previous),
            ApiErrorCode::BC0002 => new WipopApiInvalidRequestException($message, $previous),
            ApiErrorCode::BC0007 => new WipopApiUnauthorizedException($message, $previous),
            ApiErrorCode::BC0009 => new WipopApiRetryLaterException($message, $previous),
            ApiErrorCode::BC0013 => new WipopApiTimeoutException($message, $previous),
            default => new WipopException($message ?? 'Unknown API error', $code, $previous),
        };
    }
}
