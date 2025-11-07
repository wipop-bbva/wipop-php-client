<?php

declare(strict_types=1);

namespace Wipop\Domain;

enum PaymentMethodType: string
{
    case REDIRECT = 'REDIRECT';
    case THREE_DS = 'THREE_DS';
}
