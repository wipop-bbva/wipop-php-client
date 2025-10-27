<?php

declare(strict_types=1);

namespace Wipop\Checkout\Payload;

use Wipop\Customer\Customer;

final class CustomerPayload
{
    /**
     * @return array<string, array<string, string>|string>
     */
    public static function fromCustomer(?Customer $customer): array
    {
        if ($customer === null) {
            return [
                'name' => '',
                'last_name' => '',
                'email' => '',
                'phone_number' => '',
                'external_id' => '',
            ];
        }

        $payload = [
            'name' => $customer->getName(),
            'last_name' => $customer->getLastName(),
            'email' => $customer->getEmail(),
        ];

        if ($customer->getExternalId() !== null) {
            $payload['external_id'] = $customer->getExternalId();
        }

        if ($customer->getPhoneNumber() !== null) {
            $payload['phone_number'] = $customer->getPhoneNumber();
        }

        return $payload;
    }
}
