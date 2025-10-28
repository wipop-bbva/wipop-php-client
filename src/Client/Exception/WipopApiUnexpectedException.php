<?php

declare(strict_types=1);

namespace Wipop\Client\Exception;

use Throwable;

final class WipopApiUnexpectedException extends WipopApiException
{
    public const DEFAULT_MESSAGE = 'Unexpected error occurred. Please contact support.';

    public function __construct(?string $message = null, ?Throwable $previous = null)
    {
        parent::__construct($message ?? self::DEFAULT_MESSAGE, ApiErrorCode::AC000, $previous);
    }
}
