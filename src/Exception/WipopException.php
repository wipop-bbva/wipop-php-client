<?php

declare(strict_types=1);

namespace Wipop\Exception;

use Exception;
use Throwable;

class WipopException extends Exception
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
