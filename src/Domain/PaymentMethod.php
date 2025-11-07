<?php

declare(strict_types=1);

namespace Wipop\Domain;

final class PaymentMethod
{
    public ?PaymentMethodType $type = null;
    public ?string $url = null;
}
