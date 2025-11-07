<?php

declare(strict_types=1);

namespace Wipop\Domain;

use Symfony\Component\Serializer\Attribute\SerializedName;

final class Charge extends Transaction
{
    public ?Refund $refund = null;

    #[SerializedName('payment_method')]
    public ?PaymentMethod $paymentMethod = null;

    public ?Customer $customer = null;

    #[SerializedName('use_cof')]
    public ?bool $useCof = null;

    /**
     * @var null|array<string,mixed>
     */
    public ?array $metadata = null;

    #[SerializedName('terminal')]
    public ?Terminal $terminal = null;
}
