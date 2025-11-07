<?php

declare(strict_types=1);

namespace Wipop\Charge;

use InvalidArgumentException;
use Wipop\Client\Request\RequestBuilder;

use function array_key_exists;
use function is_numeric;

/**
 * Parameters for capturing a charge.
 */
final class CaptureParams extends RequestBuilder
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

        if (!array_key_exists('amount', $parameters)) {
            throw new InvalidArgumentException('Capture amount is required.');
        }

        if (isset($parameters['amount']) && !is_numeric($parameters['amount'])) {
            throw new InvalidArgumentException('Capture amount must be numeric.');
        }

        return $parameters;
    }
}
