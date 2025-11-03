<?php

declare(strict_types=1);

namespace Wipop\Client;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\ClientInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Wipop\Charge\ChargeOperation;
use Wipop\Checkout\CheckoutOperation;
use Wipop\Client\Http\GuzzleHttpClient;

final class WipopClient
{
    private readonly CheckoutOperation $checkoutOperation;
    private readonly ChargeOperation $chargeOperation;
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

        $httpAdapter = new GuzzleHttpClient($this->httpClient);

        $this->checkoutOperation = new CheckoutOperation(
            $httpAdapter,
            $this->configuration,
            $this->logger
        );
        $this->chargeOperation = new ChargeOperation(
            $httpAdapter,
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
