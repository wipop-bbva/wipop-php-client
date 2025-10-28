<?php

declare(strict_types=1);

namespace Wipop\Client\Exception;

use Throwable;

final class WipopApiTimeoutException extends WipopApiException
{
    public const DEFAULT_MESSAGE = 'Timeout exceeded. Please try again later.';

    public function __construct(?string $message = null, ?Throwable $previous = null)
    {
        parent::__construct($message ?? self::DEFAULT_MESSAGE, ApiErrorCode::BC0013, $previous);
    }
}
