<?php

declare(strict_types=1);

namespace Wipop\Operations\Checkout;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Wipop\Client\Exception\WipopApiExceptionFactory;
use Wipop\Client\Http\HttpClientInterface;
use Wipop\Client\Operation\AbstractOperation;
use Wipop\Client\WipopClientConfiguration;
use Wipop\Domain\Checkout as CheckoutResult;
use Wipop\Domain\Response\ChargeStatus;
use Wipop\Operations\Checkout\Params\CheckoutParams;

final class CheckoutOperation extends AbstractOperation
{
    public function __construct(
        HttpClientInterface $httpClient,
        WipopClientConfiguration $configuration,
        ?LoggerInterface $logger = null,
    ) {
        parent::__construct($httpClient, $configuration, $logger ?? new NullLogger());
    }

    /**
     * Creates a checkout without customer
     */
    public function createCheckout(CheckoutParams $params): CheckoutResult
    {
        $data = $this->post(
            $this->buildCheckoutPath(),
            $params->toArray()
        );

        $this->assertSuccess($data);

        return $this->hydrate(CheckoutResult::class, $data);
    }

    public function createCustomerCheckout(string $customerId, CheckoutParams $params): CheckoutResult
    {
        $data = $this->post(
            $this->buildCustomerCheckoutPath($customerId),
            $params->toArray()
        );

        $this->assertSuccess($data);

        return $this->hydrate(CheckoutResult::class, $data);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function assertSuccess(array $data): void
    {
        if (($data['status'] ?? null) !== ChargeStatus::AVAILABLE) {
            $this->getLogger()->warning('Checkout API error', ['response' => $data]);

            throw WipopApiExceptionFactory::fromPayload($data);
        }
    }

    private function buildCheckoutPath(): string
    {
        $merchantId = $this->getConfiguration()->getMerchantId();

        return sprintf('/k/v1/%s/checkouts', $merchantId);
    }

    private function buildCustomerCheckoutPath(string $customerId): string
    {
        $merchantId = $this->getConfiguration()->getMerchantId();

        return sprintf('/k/v1/%s/customers/%s/checkouts', $merchantId, $customerId);
    }
}
