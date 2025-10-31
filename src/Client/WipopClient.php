<?php

declare(strict_types=1);

namespace Wipop\Client;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\ClientInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Wipop\CardPayment\CardPayment;
use Wipop\CardPayment\CardPaymentResponse;
use Wipop\CardPayment\CardPaymentService;
use Wipop\Checkout\Checkout;
use Wipop\Checkout\CheckoutParams;
use Wipop\Checkout\CheckoutResponse;
use Wipop\Checkout\CheckoutService;
use Wipop\Client\Http\GuzzleHttpClient;
use Wipop\RecurrentPayment\RecurrentPayment;
use Wipop\RecurrentPayment\RecurrentPaymentResponse;
use Wipop\RecurrentPayment\RecurrentPaymentService;

final class WipopClient
{
    private readonly CardPaymentService $cardPaymentService;
    private readonly CheckoutService $checkoutService;
    private readonly RecurrentPaymentService $recurrentPaymentService;
    private readonly ClientConfiguration $configuration;
    private readonly ClientInterface $httpClient;
    private readonly LoggerInterface $logger;

    public function __construct(
        ClientConfiguration $configuration,
        ?LoggerInterface $logger = null,
        ?ClientInterface $httpClient = null,
    ) {
        $this->configuration = $configuration;
        $this->logger = $logger ?? new NullLogger();
        $this->httpClient = $httpClient ?? new HttpClient([
            'base_uri' => $this->configuration->getApiUrl(),
            'timeout' => $this->configuration->getHttpConfiguration()->getResponseTimeout() / 1000,
            'connect_timeout' => $this->configuration->getHttpConfiguration()->getConnectionRequestTimeout() / 1000,
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => sprintf(
                    'Basic %s',
                    base64_encode($this->configuration->getSecretKey() . ':')
                ),
            ],
        ]);
        $this->cardPaymentService = new CardPaymentService();
        $this->checkoutService = new CheckoutService(
            new GuzzleHttpClient($this->httpClient),
            $this->configuration,
            $this->logger
        );
        $this->recurrentPaymentService = new RecurrentPaymentService();
    }

    public function getConfiguration(): ClientConfiguration
    {
        return $this->configuration;
    }

    public function checkoutPayment(Checkout|CheckoutParams $checkout): CheckoutResponse
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
