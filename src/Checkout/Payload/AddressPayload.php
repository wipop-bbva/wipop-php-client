<?php

declare(strict_types=1);

namespace Wipop\Checkout\Payload;

use Wipop\Customer\Address;

final class AddressPayload
{
    /**
     * @return array{address:string, zip_code:string, city:string, state:string, country_code:string}
     */
    public static function fromAddress(Address $address): array
    {
        return [
            'address' => $address->getAddress(),
            'zip_code' => $address->getZipCode(),
            'city' => $address->getCity(),
            'state' => $address->getState(),
            'country_code' => $address->getCountryCode(),
        ];
    }
}
