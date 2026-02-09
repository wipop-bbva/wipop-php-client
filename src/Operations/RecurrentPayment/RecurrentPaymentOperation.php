<?php

declare(strict_types=1);

namespace Wipop\Operations\RecurrentPayment;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Wipop\Client\Http\HttpClientInterface;
use Wipop\Client\WipopClientConfiguration;
use Wipop\Domain\Charge;
use Wipop\Operations\Charge\ChargeOperation;
use Wipop\Operations\RecurrentPayment\Params\RecurrentPaymentParams;

// Convenience wrapper for recurrent payments.
final class RecurrentPaymentOperation
{
    private ChargeOperation $chargeOperation;

    public function __construct(
        HttpClientInterface $httpClient,
        WipopClientConfiguration $configuration,
        ?LoggerInterface $logger = null,
    ) {
        $this->chargeOperation = new ChargeOperation($httpClient, $configuration, $logger ?? new NullLogger());
    }

    public function create(RecurrentPaymentParams $params, ?string $customerId = null): Charge
    {
        return $this->chargeOperation->create($params, $customerId);
    }
}
