<?php

declare(strict_types=1);

namespace Wipop\Customer;

use DateTimeImmutable;

final class Customer
{
    public function __construct(
        private readonly string $name,
        private readonly string $lastName,
        private readonly string $email,
        private readonly ?string $publicId = null,
        private readonly ?string $externalId = null,
        private readonly ?string $phoneNumber = null,
        private readonly ?Address $address = null,
        private readonly ?DateTimeImmutable $creationDate = null
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function getPublicId(): ?string
    {
        return $this->publicId;
    }

    public function getExternalId(): ?string
    {
        return $this->externalId;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function getCreationDate(): ?DateTimeImmutable
    {
        return $this->creationDate;
    }
}
