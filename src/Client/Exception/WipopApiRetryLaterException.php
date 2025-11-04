<?php

declare(strict_types=1);

namespace Wipop\Client\Exception;

use Throwable;

final class WipopApiRetryLaterException extends WipopApiException
{
    public const DEFAULT_MESSAGE = 'The operation could not be completed. Please try again later.';

    public function __construct(?string $message = null, ?Throwable $previous = null)
    {
        parent::__construct($message ?? self::DEFAULT_MESSAGE, ApiErrorCode::BC0009, $previous);
    }
}
