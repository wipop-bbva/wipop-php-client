<?php

declare(strict_types=1);

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
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getAuthorization(): string
    {
        return $this->authorization;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getOperationType(): string
    {
        return $this->operationType;
    }

    public function getTransactionType(): string
    {
        return $this->transactionType;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function isConciliated(): bool
    {
        return $this->conciliated;
    }

    public function getCreationDate(): DateTimeImmutable
    {
        return $this->creationDate;
    }

    public function getOperationDate(): DateTimeImmutable
    {
        return $this->operationDate;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getOrderId(): OrderId
    {
        return $this->orderId;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }
}
