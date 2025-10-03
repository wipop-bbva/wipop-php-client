<?php

declare(strict_types=1);

namespace Wipop\RecurrentPayment;

use LogicException;

class RecurrentPaymentService
{
    /**
     * @todo Implement API call returning RecurrentPaymentResponse
     *
     * @throws LogicException until implemented
     */
    public function pay(RecurrentPayment $recurrentPayment): RecurrentPaymentResponse
    {
        throw new LogicException('RecurrentPaymentService::pay is not implemented yet');
    }
}
