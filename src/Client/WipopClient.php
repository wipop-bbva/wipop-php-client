<?php

declare(strict_types=1);

namespace Wipop\Client;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Wipop\Client\Http\GuzzleHttpClient;
use Wipop\Client\Http\HttpClientInterface;
use Wipop\Operations\Charge\ChargeOperation;
use Wipop\Operations\Checkout\CheckoutOperation;
use Wipop\Operations\Merchant\MerchantOperation;
use Wipop\Operations\RecurrentPayment\RecurrentPaymentOperation;

final class WipopClient
{
    private readonly CheckoutOperation $checkoutOperation;
    private readonly ChargeOperation $chargeOperation;
    private readonly MerchantOperation $merchantOperation;
    private readonly RecurrentPaymentOperation $recurrentPaymentOperation;
    private readonly WipopClientConfiguration $configuration;
    private readonly HttpClientInterface $httpClient;
    private readonly LoggerInterface $logger;

    public function __construct(
        WipopClientConfiguration $configuration,
        ?LoggerInterface $logger = null,
        ?HttpClientInterface $httpClient = null,
    ) {
        $this->configuration = $configuration;
        $this->logger = $logger ?? new NullLogger();
        $this->httpClient = $httpClient ?? new GuzzleHttpClient($this->configuration);

        $this->checkoutOperation = new CheckoutOperation(
            $this->httpClient,
            $this->configuration,
            $this->logger
        );

        $this->chargeOperation = new ChargeOperation(
            $this->httpClient,
            $this->configuration,
            $this->logger
        );

        $this->merchantOperation = new MerchantOperation(
            $this->httpClient,
            $this->configuration,
            $this->logger
        );

        $this->recurrentPaymentOperation = new RecurrentPaymentOperation(
            $this->httpClient,
            $this->configuration,
            $this->logger
        );
    }

    public function getConfiguration(): WipopClientConfiguration
    {
        return $this->configuration;
    }

    public function checkoutOperation(): CheckoutOperation
    {
        return $this->checkoutOperation;
    }

    public function chargeOperation(): ChargeOperation
    {
        return $this->chargeOperation;
    }

    public function merchantOperation(): MerchantOperation
    {
        return $this->merchantOperation;
    }

    public function recurrentPaymentOperation(): RecurrentPaymentOperation
    {
        return $this->recurrentPaymentOperation;
    }
}
