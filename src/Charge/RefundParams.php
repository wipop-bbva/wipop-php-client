<?php

declare(strict_types=1);

namespace Wipop\Charge;

use InvalidArgumentException;
use Wipop\Client\Request\RequestBuilder;

use function is_numeric;

/**
 * Parameters for refunding a charge.
 */
final class RefundParams extends RequestBuilder
{
    public function amount(float $amount): self
    {
        return $this->with('amount', $amount);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $parameters = $this->parameters();

        if (isset($parameters['amount']) && !is_numeric($parameters['amount'])) {
            throw new InvalidArgumentException('Refund amount must be numeric.');
        }

        return $parameters;
    }
}
