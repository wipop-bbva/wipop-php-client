<?php

declare(strict_types=1);

namespace Wipop\Client\Exception;

use Throwable;
use Wipop\Exception\WipopException;

final class WipopApiValidationException extends WipopException
{
    public const DEFAULT_MESSAGE = 'Invalid data: review the information provided.';

    public function __construct(?string $message = null, ?Throwable $previous = null)
    {
        parent::__construct($message ?? self::DEFAULT_MESSAGE, ApiErrorCode::VC000, $previous);
    }
}
