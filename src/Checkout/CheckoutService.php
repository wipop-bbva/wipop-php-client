<?php

declare(strict_types=1);

namespace Wipop\Checkout;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Wipop\Checkout\Payload\CheckoutPayload;
use Wipop\Checkout\Response\CheckoutResponseFactory;
use Wipop\Client\ClientConfiguration;
use Wipop\Client\Exception\WipopApiException;
use Wipop\Utils\ChargeStatus;

final class CheckoutService
{
    private readonly LoggerInterface $logger;
    private readonly CheckoutResponseFactory $checkoutResponseFactory;

    public function __construct(
        private readonly ClientInterface $httpClient,
        private readonly ClientConfiguration $configuration,
        ?LoggerInterface $logger = null,
        ?CheckoutResponseFactory $checkoutResponseFactory = null
    ) {
        $this->logger = $logger ?? new NullLogger();
        $this->checkoutResponseFactory = $checkoutResponseFactory ?? new CheckoutResponseFactory();
    }

    /**
     * @todo Implement API call returning CheckoutResponse
     */
    public function pay(Checkout $checkout): CheckoutResponse
    {
        try {
            $path = $this->getEndpointPath($checkout);
            $response = $this->httpClient->request('POST', $path, [
                'json' => CheckoutPayload::fromCheckout($checkout),
            ]);
        } catch (GuzzleException $exception) {
            $this->logger->error('Error calling checkout endpoint: ' . $exception->getMessage());

            throw new WipopApiException('HTTP error on checkout request', null, $exception);
        }

        try {
            $body = $response->getBody()->getContents();
            /** @var array<string, mixed> $data */
            $data = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            $this->logger->error('Error decoding JSON response: ' . $exception->getMessage());

            throw new WipopApiException('Error decoding JSON response', null, $exception);
        }
        $this->assertSuccess($data);

        return $this->checkoutResponseFactory->fromArray($data);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function assertSuccess(array $data): void
    {
        if (($data['status'] ?? null) !== ChargeStatus::AVAILABLE) {
            $this->logger->warning('Checkout API error', ['response' => $data]);

            throw WipopApiException::fromPayload($data);
        }
    }

    private function getEndpointPath(Checkout $checkout): string
    {
        $merchantId = $this->configuration->getMerchantId();

        if ($checkout->getCustomer()?->getPublicId() !== null) {
            return sprintf('/k/v1/%s/customers/%s/checkouts', $merchantId, $checkout->getCustomer()->getPublicId());
        }

        return sprintf('/k/v1/%s/checkouts', $merchantId);
    }
}
