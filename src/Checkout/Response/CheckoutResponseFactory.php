<?php

declare(strict_types=1);

namespace Wipop\Checkout\Response;

use DateTimeImmutable;
use JsonException;
use Wipop\Checkout\CheckoutResponse;
use Wipop\Utils\OrderId;

final class CheckoutResponseFactory
{
    private CustomerFactory $customerFactory;

    public function __construct(?CustomerFactory $customerFactory = null)
    {
        $this->customerFactory = $customerFactory ?? new CustomerFactory();
    }

    /**
     * @param array<string,mixed> $payload
     *
     * @throws JsonException
     */
    public function fromArray(array $payload): CheckoutResponse
    {
        $id = $this->expectString($payload, 'id');
        $amount = $this->expectNumeric($payload, 'amount');
        $status = $this->expectString($payload, 'status');
        $checkoutLink = $this->expectString($payload, 'checkout_link');
        $creationDate = $this->expectString($payload, 'creation_date');
        $expirationDate = $this->expectString($payload, 'expiration_date');
        $orderId = $this->expectString($payload, 'order_id');
        $description = isset($payload['description']) && is_string($payload['description'])
            ? $payload['description']
            : '';
        /** @var null|array<string, mixed> $customerPayload */
        $customerPayload = isset($payload['customer']) && is_array($payload['customer'])
            ? $payload['customer']
            : null;

        return new CheckoutResponse(
            $id,
            (float) $amount,
            $description,
            OrderId::fromString($orderId),
            $status,
            $checkoutLink,
            new DateTimeImmutable($creationDate),
            new DateTimeImmutable($expirationDate),
            $this->customerFactory->fromArray($customerPayload)
        );
    }

    /**
     * @param array<string,mixed> $payload
     *
     * @throws JsonException
     */
    private function expectString(array $payload, string $key): string
    {
        if (!isset($payload[$key]) || !is_string($payload[$key])) {
            throw new JsonException(sprintf("Expected key '%s' to be a string.", $key));
        }

        return $payload[$key];
    }

    /**
     * @param array<string,mixed> $payload
     *
     * @throws JsonException
     */
    private function expectNumeric(array $payload, string $key): float
    {
        if (!array_key_exists($key, $payload)) {
            throw new JsonException(sprintf("Expected key '%s' to be numeric.", $key));
        }

        $value = $payload[$key];

        if (!is_numeric($value)) {
            throw new JsonException(sprintf("Expected key '%s' to be numeric.", $key));
        }

        return (float) $value;
    }
}
