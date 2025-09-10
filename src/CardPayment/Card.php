<?php declare(strict_types=1);

namespace Wipop\CardPayment;

final class Card
{
    /**
     * @param string $id
     * @param string $number
     * @param string $expirationYear
     * @param string $expirationMonth
     */
    public function __construct(
        private readonly string $id,
        private readonly string $number,
        private readonly string $expirationYear,
        private readonly string $expirationMonth,
    ){}

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getNumber(): string
    {
        return $this->number;
    }

    /**
     * @return string
     */
    public function getExpirationYear(): string
    {
        return $this->expirationYear;
    }

    /**
     * @return string
     */
    public function getExpirationMonth(): string
    {
        return $this->expirationMonth;
    }
}
