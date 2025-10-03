<?php

declare(strict_types=1);

namespace Wipop\CardPayment;

use LogicException;

final class CardPaymentService
{
    /**
     * @todo Implement API call returning CardPaymentResponse
     *
     * @throws LogicException until implemented
     */
    public function pay(CardPayment $cardPayment): CardPaymentResponse
    {
        throw new LogicException('CardPaymentService::pay is not implemented yet');
    }
}
