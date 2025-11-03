<?php

declare(strict_types=1);

namespace Wipop\Charge;

use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Wipop\Client\ClientConfiguration;
use Wipop\Client\Http\HttpClientInterface;
use Wipop\Client\Operation\AbstractOperation;
use Wipop\Customer\Customer;

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

    /**
     * @return array<string, mixed>
     */
    public function create(ChargeParams $params): array
    {
        $payload = $params->toArray();
        $path = $this->buildCreatePath($params);

        return $this->post($path, $payload);
    }

    /**
     * @return array<string, mixed>
     */
    public function confirm(string $transactionId, ConfirmChargeParams $params, ?string $customerId = null): array
    {
        $path = $this->buildChargePath($transactionId, '/confirm', $customerId);

        return $this->post($path, $params->toArray());
    }

    /**
     * @return array<string, mixed>
     */
    public function refund(string $transactionId, RefundParams $params): array
    {
        $path = $this->buildChargePath($transactionId, '/refund');

        return $this->post($path, $params->toArray());
    }

    /**
     * @return array<string, mixed>
     */
    public function reversal(string $transactionId, ReversalParams $params): array
    {
        $path = $this->buildChargePath($transactionId, '/reversal');

        return $this->post($path, $params->toArray());
    }

    /**
     * @return array<string, mixed>
     */
    public function capture(string $transactionId, CaptureParams $params): array
    {
        $path = $this->buildChargePath($transactionId, '/capture');

        return $this->post($path, $params->toArray());
    }

    private function buildCreatePath(ChargeParams $params): string
    {
        $prefix = $this->resolveMethodPrefix($params->getMethod());
        $merchantId = $this->getConfiguration()->getMerchantId();

        $customer = $params->getCustomer();
        $customerId = $this->resolveCustomerPublicId($customer);

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

    private function resolveCustomerPublicId(?Customer $customer): ?string
    {
        if ($customer === null) {
            return null;
        }

        $publicId = $customer->getPublicId();

        return $publicId !== null && $publicId !== '' ? $publicId : null;
    }
}
