<?php

declare(strict_types=1);

namespace Wipop\Client\Exception;

use Throwable;

final class WipopApiBusinessException extends WipopApiException
{
    public const DEFAULT_MESSAGE = 'Business error: the operation could not be completed.';

    public function __construct(?string $message = null, ?Throwable $previous = null)
    {
        parent::__construct($message ?? self::DEFAULT_MESSAGE, ApiErrorCode::BC000, $previous);
    }
}
