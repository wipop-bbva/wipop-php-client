<?php declare(strict_types=1);

namespace Wipop\RecurrentPayment;

use DateTimeImmutable;
use Wipop\Customer\Customer;
use Wipop\Utils\OrderId;

final class RecurrentPaymentResponse
{
    public function __construct(
        private readonly string $id,
        private readonly string $method,
        private readonly string $authorization,
        private readonly string $currency,
        private readonly string $operationType,
        private readonly string $transactionType,
        private readonly string $status,
        private readonly bool $conciliated,
        private readonly DateTimeImmutable $creationDate,
        private readonly DateTimeImmutable $operationDate,
        private readonly string $description,
        private readonly OrderId $orderId,
        private readonly string $amount,
        private readonly Customer $customer,
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
    public function getAuthorization(): string
    {
        return $this->authorization;
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
     * @return bool
     */
    public function isConciliated(): bool
    {
        return $this->conciliated;
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
     * @return Customer
     */
    public function getCustomer(): Customer
    {
        return $this->customer;
    }
}
