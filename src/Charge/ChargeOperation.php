<?php

declare(strict_types=1);

namespace Wipop\Charge;

use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Wipop\Client\ClientConfiguration;
use Wipop\Client\Http\HttpClientInterface;
use Wipop\Client\Operation\AbstractOperation;
use Wipop\Domain\Charge as ChargeResult;

use function sprintf;

final class ChargeOperation extends AbstractOperation
{
    public function __construct(
        HttpClientInterface $httpClient,
        ClientConfiguration $configuration,
        ?LoggerInterface $logger = null,
    ) {
        parent::__construct($httpClient, $configuration, $logger ?? new NullLogger());
    }

    public function create(ChargeParams $params, ?string $customerId = null): ChargeResult
    {
        $payload = $params->toArray();
        $path = $this->buildCreatePath($params, $customerId);

        $data = $this->post($path, $payload);

        return $this->hydrate(ChargeResult::class, $data);
    }

    public function confirmPreauthorization(
        string $transactionId,
        CaptureParams $params,
        ?string $customerId = null
    ): ChargeResult {
        return $this->capture($transactionId, $params, $customerId);
    }

    public function reversePreauthorization(
        string $transactionId,
        ReversalParams $params,
        ?string $customerId = null
    ): ChargeResult {
        return $this->reversal($transactionId, $params, $customerId);
    }

    public function refund(string $transactionId, RefundParams $params): ChargeResult
    {
        $path = $this->buildChargePath($transactionId, '/refund');

        $data = $this->post($path, $params->toArray());

        return $this->hydrate(ChargeResult::class, $data);
    }

    public function reversal(string $transactionId, ReversalParams $params, ?string $customerId = null): ChargeResult
    {
        $path = $this->buildChargePath($transactionId, '/reversal', $customerId);

        $data = $this->post($path, $params->toArray());

        return $this->hydrate(ChargeResult::class, $data);
    }

    public function capture(string $transactionId, CaptureParams $params, ?string $customerId = null): ChargeResult
    {
        $path = $this->buildChargePath($transactionId, '/capture', $customerId);

        $data = $this->post($path, $params->toArray());

        return $this->hydrate(ChargeResult::class, $data);
    }

    private function buildCreatePath(ChargeParams $params, ?string $customerId = null): string
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
