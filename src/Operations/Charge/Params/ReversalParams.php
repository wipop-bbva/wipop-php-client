<?php

declare(strict_types=1);

namespace Wipop\Operations\Charge\Params;

use Wipop\Client\Request\RequestBuilder;

/**
 * Parameters for reversing a preauth.
 */
final class ReversalParams extends RequestBuilder
{
    public function reason(string $reason): self
    {
        return $this->with('reason', $reason);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $parameters = $this->parameters();

        if (!isset($parameters['reason'])) {
            return ['reason' => 'PRE_REVERSAL'];
        }

        return $parameters;
    }
}
