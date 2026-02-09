<?php

declare(strict_types=1);

namespace Wipop\Domain\Input;

use Wipop\Domain\Currency;
use Wipop\Domain\Origin;
use Wipop\Domain\Value\OrderId;
use Wipop\Domain\Value\Terminal;

final class Checkout
{
    public function __construct(
        private readonly float $amount,
        private readonly string $productType,
        private readonly Terminal $terminal,
        private readonly ?OrderId $orderId = null,
        private readonly ?Customer $customer = null,
        private readonly ?string $redirectUrl = null,
        private readonly ?string $description = null,
        private readonly string $currency = Currency::EUR,
        private readonly string $origin = Origin::API,
        private readonly bool $sendEmail = false,
    ) {
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getProductType(): string
    {
        return $this->productType;
    }

    public function getTerminal(): Terminal
    {
        return $this->terminal;
    }

    public function getOrderId(): ?OrderId
    {
        return $this->orderId;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function getRedirectUrl(): ?string
    {
        return $this->redirectUrl;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getOrigin(): string
    {
        return $this->origin;
    }

    public function isSendEmail(): bool
    {
        return $this->sendEmail;
    }
}
