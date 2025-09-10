<?php declare(strict_types=1);

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

    /**
     * @param float $amount
     * @param string $method
     * @param OrderId|null $orderId
     * @param Customer|null $customer
     * @param string|null $redirectUrl
     * @param string|null $description
     * @param string $currency
     * @param bool $sendEmail
     * @param string $language
     */
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
    )
    {
        $this->productType = ProductType::PAYMENT_GATEWAY;
        $this->originChannel = OriginChannel::API;
        $this->postType = 'RECURRENT';
        $this->tokenize = true;
        $this->terminal = new Terminal(0);
    }

    /**
     * @return string
     */
    public function getProductType(): string
    {
        return $this->productType;
    }

    /**
     * @return string
     */
    public function getOriginChannel(): string
    {
        return $this->originChannel;
    }

    /**
     * @return string
     */
    public function getPostType(): string
    {
        return $this->postType;
    }

    /**
     * @return bool
     */
    public function isTokenize(): bool
    {
        return $this->tokenize;
    }

    /**
     * @return Terminal
     */
    public function getTerminal(): Terminal
    {
        return $this->terminal;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return OrderId|null
     */
    public function getOrderId(): ?OrderId
    {
        return $this->orderId;
    }

    /**
     * @return Customer|null
     */
    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    /**
     * @return string|null
     */
    public function getRedirectUrl(): ?string
    {
        return $this->redirectUrl;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @return bool
     */
    public function isSendEmail(): bool
    {
        return $this->sendEmail;
    }

    /**
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->language;
    }
}
