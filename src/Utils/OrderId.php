<?php

declare(strict_types=1);

namespace Wipop\Utils;

use InvalidArgumentException;
use Stringable;

final class OrderId implements Stringable
{
    public const VALIDATION_REGEX = '^\d{4}[a-zA-Z0-9]{8}$';
    private readonly string $orderId;

    public function __construct(string $orderId)
    {
        if (!$this->isValid($orderId)) {
            throw new InvalidArgumentException("Order id '{$orderId}' is not a valid order id.");
        }

        $this->orderId = $orderId;
    }

    public function __toString()
    {
        return $this->orderId;
    }

    public function value(): string
    {
        return $this->orderId;
    }

    public static function fromString(string $orderId): self
    {
        return new self($orderId);
    }

    private function isValid(string $orderId): bool
    {
        return preg_match('/' . self::VALIDATION_REGEX . '/', $orderId) === 1;
    }
}
