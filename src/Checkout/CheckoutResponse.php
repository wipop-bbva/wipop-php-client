<?php

declare(strict_types=1);

namespace Wipop\Checkout;

use DateTimeImmutable;
use Wipop\Customer\Customer;
use Wipop\Utils\OrderId;

final class CheckoutResponse
{
    public function __construct(
        public readonly string $id,
        public readonly float $amount,
        public readonly string $description,
        public readonly OrderId $orderId,
        public readonly string $status,
        public readonly string $checkoutLink,
        public readonly DateTimeImmutable $creationDate,
        public readonly DateTimeImmutable $expirationDate,
        public readonly Customer $customer,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getOrderId(): OrderId
    {
        return $this->orderId;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getCheckoutLink(): string
    {
        return $this->checkoutLink;
    }

    public function getCreationDate(): DateTimeImmutable
    {
        return $this->creationDate;
    }

    public function getExpirationDate(): DateTimeImmutable
    {
        return $this->expirationDate;
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }
}
