<?php

declare(strict_types=1);

namespace Wipop\Client\Exception;

use Throwable;
use Wipop\Exception\WipopException;

final class WipopApiDataAccessException extends WipopException
{
    public const DEFAULT_MESSAGE = 'Data access error.';

    public function __construct(?string $message = null, ?Throwable $previous = null)
    {
        parent::__construct($message ?? self::DEFAULT_MESSAGE, ApiErrorCode::DC000, $previous);
    }
}
