<?php

declare(strict_types=1);

namespace Wipop\CardPayment;

use DateTimeImmutable;
use Wipop\Utils\OrderId;
use Wipop\Utils\PaymentMethod;

final class CardPaymentResponse
{
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

    public function getCustomerId(): string
    {
        return $this->customerId;
    }

    public function getPaymentMethod(): PaymentMethod
    {
        return $this->paymentMethod;
    }
}
