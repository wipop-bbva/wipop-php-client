<?php

declare(strict_types=1);

namespace Wipop\Tests\Checkout\Response;

use JsonException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Wipop\Checkout\CheckoutResponse;
use Wipop\Checkout\Response\CheckoutResponseFactory;
use Wipop\Customer\NullCustomer;

/**
 * @internal
 */
#[CoversClass(CheckoutResponseFactory::class)]
#[CoversClass(CheckoutResponse::class)]
class CheckoutResponseFactoryTest extends TestCase
{
    private CheckoutResponseFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new CheckoutResponseFactory();
    }

    #[Test]
    public function itCreatesCheckoutResponseFromValidPayload(): void
    {
        $payload = [
            'id' => 'ck_qwerty123',
            'amount' => '19.99',
            'status' => 'AVAILABLE',
            'checkout_link' => 'https://example.wipop.es/k/ck/abc',
            'creation_date' => '2024-10-01T08:41:08+00:00',
            'expiration_date' => '2024-10-02T08:41:08+00:00',
            'order_id' => '1234abcDEF12',
        ];

        $response = $this->factory->fromArray($payload);

        $this->assertInstanceOf(CheckoutResponse::class, $response);
        $this->assertSame('ck_qwerty123', $response->getId());
        $this->assertSame(19.99, $response->getAmount());
        $this->assertSame('', $response->getDescription());
        $this->assertSame('AVAILABLE', $response->getStatus());
        $this->assertSame('1234abcDEF12', $response->getOrderId()->value());
        $this->assertInstanceOf(NullCustomer::class, $response->getCustomer());
    }

    #[Test]
    public function itNormalizesDescriptionAndCustomerPayload(): void
    {
        $payload = [
            'id' => 'ck_has_customer',
            'amount' => 10,
            'status' => 'AVAILABLE',
            'checkout_link' => 'https://sandbox-api.wipop.es/k/ck/xyz',
            'creation_date' => '2024-05-01T08:41:08+00:00',
            'expiration_date' => '2024-05-02T08:41:08+00:00',
            'order_id' => '5678Qwerty12',
            'description' => null,
            'customer' => [
                'name' => 'Ana',
                'last_name' => 'García',
                'email' => 'ana@example.com',
            ],
        ];

        $response = $this->factory->fromArray($payload);

        $this->assertSame('', $response->getDescription());
        $customer = $response->getCustomer();
        $this->assertSame('Ana', $customer->getName());
        $this->assertSame('García', $customer->getLastName());
        $this->assertSame('ana@example.com', $customer->getEmail());
    }

    #[Test]
    public function itThrowsJsonExceptionWhenRequiredFieldIsMissing(): void
    {
        $this->expectException(JsonException::class);

        $payload = [
            'amount' => '10.00',
            'status' => 'AVAILABLE',
            'checkout_link' => 'https://example.wipop.es/k/ck/xyz',
            'creation_date' => '2024-10-01T08:41:08+00:00',
            'expiration_date' => '2024-10-02T08:41:08+00:00',
            'order_id' => '5678Qwerty12',
        ];

        $this->factory->fromArray($payload);
    }
}
