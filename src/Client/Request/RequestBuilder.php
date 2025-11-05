<?php

declare(strict_types=1);

namespace Wipop\Client\Request;

/**
 * API request Builder for request params.
 */
abstract class RequestBuilder
{
    /**
     * @var array<string, mixed>
     */
    private array $parameters = [];

    protected function with(string $name, mixed $value): static
    {
        $this->parameters[$name] = $value;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    protected function parameters(): array
    {
        return $this->parameters;
    }
}
