<?php declare(strict_types=1);

namespace Wipop\Customer;

use \DateTimeImmutable as DateTimeImmutable;

final class Customer
{
    /**
     * @param string $name
     * @param string $lastName
     * @param string $email
     * @param string|null $publicId
     * @param string|null $externalId
     * @param string|null $phoneNumber
     * @param Address|null $address
     * @param DateTimeImmutable|null $creationDate
     */
    public function __construct(
      private readonly string $name,
      private readonly string $lastName,
      private readonly string $email,
      private readonly ?string $publicId = null,
      private readonly ?string $externalId = null,
      private readonly ?string $phoneNumber = null,
      private readonly ?Address $address = null,
      private readonly ?DateTimeImmutable $creationDate = null
    ) {}

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string|null
     */
    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    /**
     * @return string|null
     */
    public function getPublicId(): ?string
    {
        return $this->publicId;
    }

    /**
     * @return string|null
     */
    public function getExternalId(): ?string
    {
        return $this->externalId;
    }

    /**
     * @return Address|null
     */
    public function getAddress(): ?Address
    {
        return $this->address;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getCreationDate(): ?DateTimeImmutable
    {
        return $this->creationDate;
    }
}