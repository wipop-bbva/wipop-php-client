<?php

declare(strict_types=1);

namespace Wipop\Domain\Input;

use Wipop\Domain\Currency;
use Wipop\Domain\Language;
use Wipop\Domain\ProductType;
use Wipop\Domain\Value\OrderId;
use Wipop\Domain\Value\Terminal;

final class CardPayment
{
    private readonly string $method;

    public function __construct(
        private readonly float $amount,
        private readonly Terminal $terminal,
        private readonly ?OrderId $orderId = null,
        private readonly ?Customer $customer = null,
        private readonly ?string $redirectUrl = null,
        private readonly ?string $description = null,
        private readonly bool $tokenize = false,
        private readonly string $currency = Currency::EUR,
        private readonly bool $sendEmail = false,
        private readonly string $language = Language::SPANISH,
        private readonly string $originChannel = OriginChannel::API,
        private readonly string $productType = ProductType::PAYMENT_GATEWAY,
    ) {
        $this->method = PaymentMethod::CARD;
    }

    public function getAmount(): float
    {
        return $this->amount;
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

    public function tokenize(): bool
    {
        return $this->tokenize;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function isSendEmail(): bool
    {
        return $this->sendEmail;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function getOriginChannel(): string
    {
        return $this->originChannel;
    }

    public function getProductType(): string
    {
        return $this->productType;
    }

    public function getMethod(): string
    {
        return $this->method;
    }
}
