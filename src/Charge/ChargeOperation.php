<?php

declare(strict_types=1);

namespace Wipop\Charge;

use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Wipop\Client\ClientConfiguration;
use Wipop\Client\Http\HttpClientInterface;
use Wipop\Client\Operation\AbstractOperation;

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
    public function create(ChargeParams $params, ?string $customerId = null): array
    {
        $payload = $params->toArray();
        $path = $this->buildCreatePath($params, $customerId);

        return $this->post($path, $payload);
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


    private function resolveMethodPrefix(string $method): string
    {
        return match ($method) {
            ChargeMethod::CARD => '/c',
            ChargeMethod::BIZUM => '/b',
            default => throw new InvalidArgumentException(sprintf('Unsupported charge method "%s".', $method)),
        };
    }
}
