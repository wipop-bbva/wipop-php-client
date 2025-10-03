<?php

declare(strict_types=1);

namespace Wipop\CardPayment;

final class Card
{
    public function __construct(
        private readonly string $id,
        private readonly string $number,
        private readonly string $expirationYear,
        private readonly string $expirationMonth,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function getExpirationYear(): string
    {
        return $this->expirationYear;
    }

    public function getExpirationMonth(): string
    {
        return $this->expirationMonth;
    }
}
