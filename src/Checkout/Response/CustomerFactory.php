<?php

declare(strict_types=1);

namespace Wipop\Checkout\Response;

use DateTimeImmutable;
use Wipop\Customer\Customer;

final class CustomerFactory
{
    private AddressFactory $addressFactory;

    public function __construct(?AddressFactory $addressFactory = null)
    {
        $this->addressFactory = $addressFactory ?? new AddressFactory();
    }

    /**
     * @param null|array<string,mixed> $payload
     */
    public function fromArray(?array $payload): Customer
    {
        if ($payload === null) {
            return new Customer(
                '',
                '',
                '',
            );
        }

        $name = isset($payload['name']) && is_string($payload['name'])
            ? $payload['name']
            : '';
        $lastName = isset($payload['last_name']) && is_string($payload['last_name'])
            ? $payload['last_name']
            : '';
        $email = isset($payload['email']) && is_string($payload['email'])
            ? $payload['email']
            : '';
        $publicId = isset($payload['public_id']) && is_string($payload['public_id'])
            ? $payload['public_id']
            : null;
        $externalId = isset($payload['external_id']) && is_string($payload['external_id'])
            ? $payload['external_id']
            : null;
        $phoneNumber = isset($payload['phone_number']) && is_string($payload['phone_number'])
            ? $payload['phone_number']
            : null;
        /** @var null|array<string, mixed> $addressPayload */
        $addressPayload = isset($payload['address']) && is_array($payload['address'])
            ? $payload['address'] : null;
        $creationDate = isset($payload['creation_date']) && is_string($payload['creation_date'])
            ? new DateTimeImmutable($payload['creation_date'])
            : null;

        return new Customer(
            $name,
            $lastName,
            $email,
            $publicId,
            $externalId,
            $phoneNumber,
            $this->addressFactory->fromArray($addressPayload),
            $creationDate,
        );
    }
}
