<?php

declare(strict_types=1);

namespace Wipop\Domain;

final class OriginChannel
{
    /**
     * Recurrent payment request flag
     */
    public const API = 'API';
    /**
     * Payment link creation request flag
     */
    public const PAYMENT_LINK = 'PAYMENT_LINK';
}
