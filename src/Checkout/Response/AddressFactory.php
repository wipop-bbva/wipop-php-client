<?php

declare(strict_types=1);

namespace Wipop\Checkout\Response;

use Wipop\Customer\Address;

final class AddressFactory
{
    /**
     * @param null|array<string,mixed> $payload
     */
    public function fromArray(?array $payload): ?Address
    {
        if ($payload === null) {
            return null;
        }

        $address = isset($payload['address']) && is_string($payload['address']) ? $payload['address'] : '';
        $zipCode = isset($payload['zip_code']) && is_string($payload['zip_code']) ? $payload['zip_code'] : '';
        $city = isset($payload['city']) && is_string($payload['city']) ? $payload['city'] : '';
        $state = isset($payload['state']) && is_string($payload['state']) ? $payload['state'] : '';
        $countryCode = isset($payload['country_code']) && is_string($payload['country_code'])
            ? $payload['country_code'] : '';

        return new Address(
            $address,
            $zipCode,
            $city,
            $state,
            $countryCode
        );
    }
}
