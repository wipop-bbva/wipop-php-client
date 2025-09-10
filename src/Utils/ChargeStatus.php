<?php declare(strict_types=1);

namespace Utils;

class ChargeStatus
{
    public const AVAILABLE = 'AVAILABLE';
    public const CHARGE_PENDING = 'CHARGE_PENDING';
    public const IN_PROGRESS = 'IN_PROGRESS';
    public const COMPLETED = 'COMPLETED';
    public const FAILED = 'FAILED';
    public const ERROR = 'ERROR';
}
