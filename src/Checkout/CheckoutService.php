<?php

declare(strict_types=1);

namespace Wipop\Checkout;

use LogicException;

final class CheckoutService
{
    /**
     * @todo Implement API call returning CheckoutResponse
     *
     * @throws LogicException until implemented
     */
    public function pay(Checkout $checkout): CheckoutResponse
    {
        throw new LogicException('CheckoutService::pay is not implemented yet');
    }
}
