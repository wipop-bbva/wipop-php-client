<?php

declare(strict_types=1);

namespace Wipop\Domain;

enum TransactionStatus: string
{
    case CHARGE_PENDING = 'CHARGE_PENDING';
    case ERROR = 'ERROR';
    case FAILED = 'FAILED';
    case COMPLETED = 'COMPLETED';
    case IN_PROGRESS = 'IN_PROGRESS';
}
