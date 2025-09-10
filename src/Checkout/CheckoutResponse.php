<?php declare(strict_types=1);

namespace Wipop\Checkout;

use \DateTimeImmutable as DateTimeImmutable;
use Wipop\Customer\Customer;
use Wipop\Utils\OrderId;

final class CheckoutResponse
{

    /**
     * @param string $id
     * @param float $amount
     * @param string $description
     * @param OrderId $orderId
     * @param string $status
     * @param string $checkoutLink
     * @param DateTimeImmutable $creationDate
     * @param DateTimeImmutable $expirationDate
     * @param Customer $customer
     */
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
    ) {}

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
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
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return OrderId
     */
    public function getOrderId(): OrderId
    {
        return $this->orderId;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getCheckoutLink(): string
    {
        return $this->checkoutLink;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreationDate(): DateTimeImmutable
    {
        return $this->creationDate;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getExpirationDate(): DateTimeImmutable
    {
        return $this->expirationDate;
    }

    /**
     * @return Customer
     */
    public function getCustomer(): Customer
    {
        return $this->customer;
    }
}