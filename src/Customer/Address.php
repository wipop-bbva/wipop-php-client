<?php declare(strict_types=1);

namespace Wipop\Customer;

final class Address
{
    /**
     * @param string $address
     * @param string $zipCode
     * @param string $city
     * @param string $state
     * @param string $countryCode
     */
    public function __construct(
      private readonly string $address,
      private readonly string $zipCode,
      private readonly string $city,
      private readonly string $state,
      private readonly string $countryCode
    ) {}

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @return string
     */
    public function getZipCode(): string
    {
        return $this->zipCode;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @return string
     */
    public function getCountryCode(): string
    {
        return $this->countryCode;
    }
}