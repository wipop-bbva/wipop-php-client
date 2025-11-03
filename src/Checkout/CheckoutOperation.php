<?php

declare(strict_types=1);

namespace Wipop\Checkout;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Wipop\Checkout\Response\CheckoutResponseFactory;
use Wipop\Client\ClientConfiguration;
use Wipop\Client\Exception\WipopApiExceptionFactory;
use Wipop\Client\Http\HttpClientInterface;
use Wipop\Client\Operation\AbstractOperation;
use Wipop\Utils\ChargeStatus;

final class CheckoutOperation extends AbstractOperation
{
    private readonly CheckoutResponseFactory $checkoutResponseFactory;

    public function __construct(
        HttpClientInterface $httpClient,
        ClientConfiguration $configuration,
        ?LoggerInterface $logger = null,
    ) {
        parent::__construct($httpClient, $configuration, $logger ?? new NullLogger());
        $this->checkoutResponseFactory = new CheckoutResponseFactory();
    }

    /**
     * Creates a checkout using either the domain object or its params representation.
     */
    public function create(Checkout|CheckoutParams $checkout): CheckoutResponse
    {
        $params = $checkout instanceof CheckoutParams ? $checkout : CheckoutParams::fromCheckout($checkout);

        $data = $this->post(
            $this->getEndpointPath($params),
            $params->toArray()
        );

        $this->assertSuccess($data);

        return $this->checkoutResponseFactory->fromArray($data);
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

    private function getEndpointPath(CheckoutParams $checkout): string
    {
        $merchantId = $this->getConfiguration()->getMerchantId();

        if ($checkout->getCustomer()?->getPublicId() !== null) {
            return sprintf('/k/v1/%s/customers/%s/checkouts', $merchantId, $checkout->getCustomer()->getPublicId());
        }

        return sprintf('/k/v1/%s/checkouts', $merchantId);
    }
}
