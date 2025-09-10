<?php declare(strict_types=1);

namespace Wipop\Checkout;

use Wipop\Customer\Customer;
use Wipop\Utils\Currency;
use Wipop\Utils\OrderId;
use Wipop\Utils\Terminal;

final class Checkout
{
    /**
     * @param float $amount
     * @param string $productType
     * @param Terminal $terminal
     * @param ?OrderId $orderId
     * @param ?Customer $customer
     * @param ?string $redirectUrl
     * @param ?string $description
     * @param string $currency
     * @param string $origin
     * @param bool $sendEmail
     */
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
    ) {}

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
    public function getProductType(): string
    {
        return $this->productType;
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
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @return string
     */
    public function getOrigin(): string
    {
        return $this->origin;
    }

    /**
     * @return bool
     */
    public function isSendEmail(): bool
    {
        return $this->sendEmail;
    }
}
