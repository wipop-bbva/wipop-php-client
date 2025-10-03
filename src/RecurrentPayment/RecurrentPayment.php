<?php

declare(strict_types=1);

namespace Wipop\RecurrentPayment;

use Wipop\CardPayment\OriginChannel;
use Wipop\Customer\Customer;
use Wipop\Utils\Currency;
use Wipop\Utils\Language;
use Wipop\Utils\OrderId;
use Wipop\Utils\ProductType;
use Wipop\Utils\Terminal;

final class RecurrentPayment
{
    private readonly string $productType;
    private readonly string $originChannel;
    private readonly string $postType;
    private readonly bool $tokenize;
    private readonly Terminal $terminal;

    public function __construct(
        private readonly float $amount,
        private readonly string $method,
        private readonly ?OrderId $orderId = null,
        private readonly ?Customer $customer = null,
        private readonly ?string $redirectUrl = null,
        private readonly ?string $description = null,
        private readonly string $currency = Currency::EUR,
        private readonly bool $sendEmail = false,
        private readonly string $language = Language::SPANISH,
    ) {
        $this->productType = ProductType::PAYMENT_GATEWAY;
        $this->originChannel = OriginChannel::API;
        $this->postType = 'RECURRENT';
        $this->tokenize = true;
        $this->terminal = new Terminal(0);
    }

    public function getProductType(): string
    {
        return $this->productType;
    }

    public function getOriginChannel(): string
    {
        return $this->originChannel;
    }

    public function getPostType(): string
    {
        return $this->postType;
    }

    public function isTokenize(): bool
    {
        return $this->tokenize;
    }

    public function getTerminal(): Terminal
    {
        return $this->terminal;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getMethod(): string
    {
        return $this->method;
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

    public function isSendEmail(): bool
    {
        return $this->sendEmail;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }
}
