<?php

declare(strict_types=1);

namespace Wipop\Client\Exception;

use Throwable;

final class WipopApiAuthenticationException extends WipopApiException
{
    public const DEFAULT_MESSAGE = 'The operation could not be authenticated.';

    public function __construct(?string $message = null, ?Throwable $previous = null)
    {
        parent::__construct($message ?? self::DEFAULT_MESSAGE, ApiErrorCode::BC0001, $previous);
    }
}
