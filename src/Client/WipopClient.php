<?php

declare(strict_types=1);

namespace Wipop\Client;

use Wipop\CardPayment\CardPayment;
use Wipop\CardPayment\CardPaymentResponse;
use Wipop\CardPayment\CardPaymentService;
use Wipop\Checkout\Checkout;
use Wipop\Checkout\CheckoutResponse;
use Wipop\Checkout\CheckoutService;
use Wipop\RecurrentPayment\RecurrentPayment;
use Wipop\RecurrentPayment\RecurrentPaymentResponse;
use Wipop\RecurrentPayment\RecurrentPaymentService;

final class WipopClient
{
    private readonly CardPaymentService $cardPaymentService;
    private readonly CheckoutService $checkoutService;
    private readonly RecurrentPaymentService $recurrentPaymentService;
    private readonly ClientConfiguration $configuration;

    public function __construct(ClientConfiguration $configuration)
    {
        $this->configuration = $configuration;
        $this->cardPaymentService = new CardPaymentService();
        $this->checkoutService = new CheckoutService();
        $this->recurrentPaymentService = new RecurrentPaymentService();
    }

    public function getConfiguration(): ClientConfiguration
    {
        return $this->configuration;
    }

    public function checkoutPayment(Checkout $checkout): CheckoutResponse
    {
        return $this->checkoutService->pay($checkout);
    }

    public function cardPayment(CardPayment $cardPayment): CardPaymentResponse
    {
        return $this->cardPaymentService->pay($cardPayment);
    }

    public function recurrentPayment(RecurrentPayment $recurrentPayment): RecurrentPaymentResponse
    {
        return $this->recurrentPaymentService->pay($recurrentPayment);
    }
}
