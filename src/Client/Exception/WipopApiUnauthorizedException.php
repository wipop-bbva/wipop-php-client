<?php

declare(strict_types=1);

namespace Wipop\Client\Exception;

use Throwable;
use Wipop\Exception\WipopException;

final class WipopApiUnauthorizedException extends WipopException
{
    public const DEFAULT_MESSAGE = 'The requested operation is not allowed.';

    public function __construct(?string $message = null, ?Throwable $previous = null)
    {
        parent::__construct($message ?? self::DEFAULT_MESSAGE, ApiErrorCode::BC0007, $previous);
    }
}
