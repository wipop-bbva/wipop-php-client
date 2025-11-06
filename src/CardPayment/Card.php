<?php

declare(strict_types=1);

namespace Wipop\CardPayment;

final class Card
{
    public function __construct(
        private readonly string $cardNumber,
        private readonly string $expirationYear,
        private readonly string $expirationMonth,
        private readonly string $holderName,
        private readonly string $cvv2,
    ) {
    }

    public function getCardNumber(): string
    {
        return $this->cardNumber;
    }

    public function getExpirationYear(): string
    {
        return $this->expirationYear;
    }

    public function getExpirationMonth(): string
    {
        return $this->expirationMonth;
    }

    public function getHolderName(): string
    {
        return $this->holderName;
    }

    public function getCvv2(): string
    {
        return $this->cvv2;
    }
}
