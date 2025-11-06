<?php

declare(strict_types=1);

namespace Wipop\Domain;

use DateTimeImmutable;
use Symfony\Component\Serializer\Attribute\SerializedName;

class Transaction
{
    public ?string $id = null;
    public ?string $method = null;
    public ?float $amount = null;

    #[SerializedName('creation_date')]
    public ?DateTimeImmutable $creationDate = null;

    #[SerializedName('operation_date')]
    public ?DateTimeImmutable $operationDate = null;

    public ?TransactionStatus $status = null;
    public ?string $description = null;

    #[SerializedName('transaction_type')]
    public ?string $transactionType = null;

    #[SerializedName('operation_type')]
    public ?string $operationType = null;

    #[SerializedName('error_message')]
    public ?string $errorMessage = null;

    #[SerializedName('error_code')]
    public ?string $errorCode = null;

    public ?Card $card = null;

    public ?string $authorization = null;

    #[SerializedName('order_id')]
    public ?string $orderId = null;

    #[SerializedName('customer_id')]
    public ?string $customerId = null;

    #[SerializedName('due_date')]
    public ?DateTimeImmutable $dueDate = null;

    public ?string $currency = null;

    #[SerializedName('origin_channel')]
    public ?string $originChannel = null;
}
