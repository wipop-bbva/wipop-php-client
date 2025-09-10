<?php declare(strict_types=1);

namespace Wipop\CardPayment;

use \DateTimeImmutable as DateTimeImmutable;
use Wipop\Utils\PaymentMethod;
use Wipop\Utils\OrderId;

final class CardPaymentResponse
{

    /**
     * @param string $id
     * @param string $method
     * @param string $currency
     * @param string $operationType
     * @param string $transactionType
     * @param string $status
     * @param DateTimeImmutable $creationDate
     * @param DateTimeImmutable $operationDate
     * @param string $description
     * @param OrderId $orderId
     * @param string $amount
     * @param string $customerId
     * @param PaymentMethod $paymentMethod
     */
    public function __construct(
      private readonly string $id,
      private readonly string $currency,
      private readonly string $operationType,
      private readonly string $transactionType,
      private readonly string $status,
      private readonly DateTimeImmutable $creationDate,
      private readonly DateTimeImmutable $operationDate,
      private readonly string $description,
      private readonly OrderId $orderId,
      private readonly string $amount,
      private readonly string $customerId,
      private readonly PaymentMethod $paymentMethod,
      private readonly string $method = PaymentMethod::CARD,
    )
    {}

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
    public function getMethod(): string
    {
        return $this->method;
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
    public function getOperationType(): string
    {
        return $this->operationType;
    }

    /**
     * @return string
     */
    public function getTransactionType(): string
    {
        return $this->transactionType;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
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
    public function getOperationDate(): DateTimeImmutable
    {
        return $this->operationDate;
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
    public function getAmount(): string
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    public function getCustomerId(): string
    {
        return $this->customerId;
    }

    /**
     * @return PaymentMethod
     */
    public function getPaymentMethod(): PaymentMethod
    {
        return $this->paymentMethod;
    }
}
