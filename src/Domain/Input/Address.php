<?php

declare(strict_types=1);

namespace Wipop\Domain\Input;

final class Address
{
    public function __construct(
        private readonly string $address,
        private readonly string $zipCode,
        private readonly string $city,
        private readonly string $state,
        private readonly string $countryCode
    ) {
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getZipCode(): string
    {
        return $this->zipCode;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }
}
