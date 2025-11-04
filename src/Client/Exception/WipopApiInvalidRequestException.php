<?php

declare(strict_types=1);

namespace Wipop\Client\Exception;

use Throwable;

final class WipopApiInvalidRequestException extends WipopApiException
{
    public const DEFAULT_MESSAGE = 'The operation could not be completed. Review the submitted data.';

    public function __construct(?string $message = null, ?Throwable $previous = null)
    {
        parent::__construct($message ?? self::DEFAULT_MESSAGE, ApiErrorCode::BC0002, $previous);
    }
}
