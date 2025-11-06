<?php

declare(strict_types=1);

namespace Wipop\Client;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Wipop\Charge\ChargeOperation;
use Wipop\Checkout\CheckoutOperation;
use Wipop\Client\Http\GuzzleHttpClient;
use Wipop\Client\Http\HttpClientInterface;

final class WipopClient
{
    private readonly CheckoutOperation $checkoutOperation;
    private readonly ChargeOperation $chargeOperation;
    private readonly ClientConfiguration $configuration;
    private readonly HttpClientInterface $httpClient;
    private readonly LoggerInterface $logger;

    public function __construct(
        ClientConfiguration $configuration,
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
    }

    public function getConfiguration(): ClientConfiguration
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
}
