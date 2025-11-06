<?php

declare(strict_types=1);

namespace Wipop\Domain;

use DateTimeImmutable;
use Symfony\Component\Serializer\Attribute\SerializedName;

final class Checkout
{
    public ?string $id = null;
    public ?float $amount = null;
    public ?string $description = null;

    #[SerializedName('order_id')]
    public ?string $orderId = null;

    public ?string $currency = null;
    public ?string $status = null;

    #[SerializedName('checkout_link')]
    public ?string $checkoutLink = null;

    #[SerializedName('expiration_date')]
    public ?DateTimeImmutable $expirationDate = null;

    #[SerializedName('creation_date')]
    public ?DateTimeImmutable $creationDate = null;

    public ?Customer $customer = null;

    #[SerializedName('customer_id')]
    public ?string $customerId = null;
}
