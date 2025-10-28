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

    public function getApiCode(): ?string
    {
        return $this->apiCode;
    }
}
