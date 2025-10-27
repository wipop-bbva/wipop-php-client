<?php

declare(strict_types=1);

namespace Wipop\Utils;

use Stringable;

final class Terminal implements Stringable
{
    public function __construct(
        private readonly int $id
    ) {
    }

    public function __toString(): string
    {
        return (string) $this->id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function value(): int
    {
        return $this->id;
    }
}
