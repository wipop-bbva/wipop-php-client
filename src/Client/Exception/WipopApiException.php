<?php

declare(strict_types=1);

namespace Wipop\Client\Exception;

use Exception;
use Throwable;

class WipopApiException extends Exception
{
    private ?string $apiCode;

    public function __construct(string $message, ?string $apiCode = null, ?Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
        $this->apiCode = $apiCode;
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function fromPayload(array $payload, ?Throwable $previous = null): self
    {
        $responseCode = isset($payload['response_code']) && is_array($payload['response_code'])
            ? $payload['response_code']
            : [];

        $code = isset($responseCode['code']) && is_string($responseCode['code'])
            ? $responseCode['code']
            : null;

        $defaultMessage = $payload['detail'] ?? ($payload['status'] ?? 'Unknown API error');
        $message = isset($responseCode['message']) && is_string($responseCode['message'])
            ? $responseCode['message']
            : (is_string($defaultMessage) ? $defaultMessage : 'Unknown API error');

        if ($code !== null) {
            $message = self::messageForCode($code, $message);
        }

        return new self($message, $code, $previous);
    }

    public function getApiCode(): ?string
    {
        return $this->apiCode;
    }

    private static function messageForCode(string $code, string $default): string
    {
        return match ($code) {
            ApiErrorCode::AC000 => 'Unexpected error occurred. Please contact support.',
            ApiErrorCode::BC000 => 'Business error: the operation could not be completed.',
            ApiErrorCode::DC000 => 'Data access error.',
            ApiErrorCode::VC000 => 'Invalid data: review the information provided.',
            ApiErrorCode::BC0001 => 'The operation could not be authenticated.',
            ApiErrorCode::BC0002 => 'The operation could not be completed. Review the submitted data.',
            ApiErrorCode::BC0007 => 'The requested operation is not allowed.',
            ApiErrorCode::BC0009 => 'The operation could not be completed. Please try again later.',
            ApiErrorCode::BC0013 => 'Timeout exceeded. Please try again later.',
            default => $default,
        };
    }
}
