<?php

declare(strict_types=1);

namespace Wipop\Operations\Charge;

use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Wipop\Client\Http\HttpClientInterface;
use Wipop\Client\Operation\AbstractOperation;
use Wipop\Client\WipopClientConfiguration;
use Wipop\Domain\Charge;
use Wipop\Domain\ChargeMethod;
use Wipop\Operations\Charge\Params\CaptureParams;
use Wipop\Operations\Charge\Params\ConfirmChargeParams;
use Wipop\Operations\Charge\Params\CreateChargeParams;
use Wipop\Operations\Charge\Params\RefundParams;
use Wipop\Operations\Charge\Params\ReversalParams;

use function sprintf;

final class ChargeOperation extends AbstractOperation
{
    public function __construct(
        HttpClientInterface $httpClient,
        WipopClientConfiguration $configuration,
        ?LoggerInterface $logger = null,
    ) {
        parent::__construct($httpClient, $configuration, $logger ?? new NullLogger());
    }

    public function create(CreateChargeParams $params, ?string $customerId = null): Charge
    {
        $payload = $params->toArray();
        $path = $this->buildCreatePath($params, $customerId);

        $data = $this->post($path, $payload);

        return $this->hydrate(Charge::class, $data);
    }

    public function createCustomerCharge(string $customerId, CreateChargeParams $params): Charge
    {
        return $this->create($params, $customerId);
    }

    public function confirm(string $transactionId, ConfirmChargeParams $params, ?string $customerId = null): Charge
    {
        $path = $this->buildChargePath($transactionId, '/confirm', $customerId);

        $data = $this->post($path, $params->toArray());

        return $this->hydrate(Charge::class, $data);
    }

    public function refund(string $transactionId, RefundParams $params): Charge
    {
        $path = $this->buildChargePath($transactionId, '/refund');

        $data = $this->post($path, $params->toArray());

        return $this->hydrate(Charge::class, $data);
    }

    public function reversal(string $transactionId, ReversalParams $params, ?string $customerId = null): Charge
    {
        $path = $this->buildChargePath($transactionId, '/reversal', $customerId);

        $data = $this->post($path, $params->toArray());

        return $this->hydrate(Charge::class, $data);
    }

    public function capture(string $transactionId, CaptureParams $params, ?string $customerId = null): Charge
    {
        $path = $this->buildChargePath($transactionId, '/capture', $customerId);

        $data = $this->post($path, $params->toArray());

        return $this->hydrate(Charge::class, $data);
    }

    private function buildCreatePath(CreateChargeParams $params, ?string $customerId = null): string
    {
        $prefix = $this->resolveMethodPrefix($params->getMethod());
        $merchantId = $this->getConfiguration()->getMerchantId();

        if ($customerId !== null) {
            return sprintf('%s/v1/%s/customers/%s/charges', $prefix, $merchantId, $customerId);
        }

        return sprintf('%s/v1/%s/charges', $prefix, $merchantId);
    }

    private function buildChargePath(string $transactionId, string $suffix, ?string $customerId = null): string
    {
        $merchantId = $this->getConfiguration()->getMerchantId();

        if ($customerId !== null) {
            return sprintf(
                '/c/v1/%s/customers/%s/charges/%s%s',
                $merchantId,
                $customerId,
                $transactionId,
                $suffix
            );
        }

        return sprintf('/c/v1/%s/charges/%s%s', $merchantId, $transactionId, $suffix);
    }

    private function resolveMethodPrefix(string $method): string
    {
        return match ($method) {
            ChargeMethod::CARD => '/c',
            ChargeMethod::BIZUM => '/b',
            default => throw new InvalidArgumentException(sprintf('Unsupported charge method "%s".', $method)),
        };
    }
}
