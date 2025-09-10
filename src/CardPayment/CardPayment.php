<?php declare(strict_types=1);

namespace Wipop\CardPayment;

use Wipop\Utils\PaymentMethod;
use Wipop\Customer\Customer;
use Wipop\Utils\Currency;
use Wipop\Utils\Language;
use Wipop\Utils\OrderId;
use Wipop\Utils\ProductType;
use Wipop\Utils\Terminal;

final class CardPayment
{
    private  readonly string $method;

    /**
     * @param float $amount
     * @param Terminal $terminal
     * @param OrderId|null $orderId
     * @param Customer|null $customer
     * @param string|null $redirectUrl
     * @param string|null $description
     * @param bool $tokenize
     * @param string $currency
     * @param bool $sendEmail
     * @param string $language
     * @param string $originChannel
     * @param string $productType
     */
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
    )
    {
        $this->method = PaymentMethod::CARD;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @return Terminal
     */
    public function getTerminal(): Terminal
    {
        return $this->terminal;
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
     * @return bool
     */
    public function tokenize(): bool
    {
        return $this->tokenize;
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
    public function getProductType(): string
    {
        return $this->productType;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }
}
