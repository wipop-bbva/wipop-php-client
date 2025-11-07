<?php

declare(strict_types=1);

namespace Wipop\Tests\Checkout;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use JsonException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Wipop\Checkout\CheckoutOperation;
use Wipop\Checkout\CheckoutParams;
use Wipop\Checkout\Origin;
use Wipop\Client\ClientConfiguration;
use Wipop\Client\Environment;
use Wipop\Client\Exception\ApiErrorCode;
use Wipop\Client\Exception\WipopApiBusinessException;
use Wipop\Client\Exception\WipopApiException;
use Wipop\Client\Http\GuzzleHttpClient;
use Wipop\Customer\Customer;
use Wipop\Domain\Checkout as CheckoutResult;
use Wipop\Utils\ChargeStatus;
use Wipop\Utils\Currency;
use Wipop\Utils\Language;
use Wipop\Utils\OrderId;
use Wipop\Utils\ProductType;
use Wipop\Utils\Terminal;

use function is_float;
use function json_decode;
use function json_encode;
use function sprintf;

/**
 * @internal
 */
#[CoversClass(CheckoutOperation::class)]
class CheckoutOperationTest extends TestCase
{
    private const MERCHANT_ID = 'm1234567890123456789';
    private const ORDER_ID = '1234ABCDEFGH';

    #[Test]
    public function itSendsCheckoutRequestForGuestCustomer(): void
    {
        $history = [];
        $operation = $this->createOperationWithMockResponses([$this->successResponse()], $history);

        $checkout = (new CheckoutParams())
            ->amount(100.0)
            ->productType(ProductType::PAYMENT_GATEWAY)
            ->terminal(new Terminal(1))
            ->orderId(OrderId::fromString(self::ORDER_ID))
            ->redirectUrl('https://example.com/redirect')
            ->description('Test checkout')
        ;

        $response = $operation->create($checkout);

        $this->assertCheckoutRequest(
            $history,
            '/k/v1/' . self::MERCHANT_ID . '/checkouts',
            [
                'amount' => 100.0,
                'product_type' => ProductType::PAYMENT_GATEWAY,
                'terminal' => ['id' => 1],
                'customer' => [
                    'name' => '',
                    'last_name' => '',
                    'email' => '',
                    'phone_number' => '',
                    'external_id' => '',
                ],
                'redirect_url' => 'https://example.com/redirect',
                'description' => 'Test checkout',
                'order_id' => self::ORDER_ID,
            ]
        );

        $this->assertCheckoutResponse($response, false);
    }

    #[Test]
    public function itSendsCheckoutRequestForExistingCustomer(): void
    {
        $history = [];
        $customer = new Customer(
            name: 'Ana',
            lastName: 'García',
            email: 'ana.garcia@example.com',
            publicId: 'ext999',
            externalId: '123456',
            phoneNumber: '+34611111111'
        );

        $operation = $this->createOperationWithMockResponses([$this->successResponse([
            'description' => 'Customer checkout',
            'customer' => [
                'name' => 'Ana',
                'last_name' => 'García',
                'email' => 'ana.garcia@example.com',
                'public_id' => 'ext999',
                'external_id' => '123456',
                'phone_number' => '+34611111111',
                'address' => null,
            ],
        ])], $history);

        $checkout = (new CheckoutParams())
            ->amount(150.0)
            ->productType(ProductType::PAYMENT_GATEWAY)
            ->terminal(new Terminal(1))
            ->orderId(OrderId::fromString(self::ORDER_ID))
            ->customer($customer)
            ->description('Customer checkout')
        ;

        $response = $operation->create($checkout);

        $this->assertCheckoutRequest(
            $history,
            '/k/v1/' . self::MERCHANT_ID . '/customers/ext999/checkouts',
            [
                'amount' => 150.0,
                'currency' => Currency::EUR,
                'origin' => Origin::API,
                'send_email' => false,
                'language' => Language::SPANISH,
                'product_type' => ProductType::PAYMENT_GATEWAY,
                'terminal' => ['id' => 1],
                'customer' => [
                    'name' => 'Ana',
                    'last_name' => 'García',
                    'email' => 'ana.garcia@example.com',
                    'external_id' => '123456',
                    'phone_number' => '+34611111111',
                ],
                'description' => 'Customer checkout',
                'order_id' => self::ORDER_ID,
            ]
        );

        $this->assertInstanceOf(CheckoutResult::class, $response);
        $this->assertSame('ext999', $response->customer?->publicId);
        $this->assertSame('Ana', $response->customer?->name);
        $this->assertSame('Customer checkout', $response->description);
    }

    #[Test]
    public function itThrowsWipopApiExceptionOnHttpFail(): void
    {
        $history = [];
        $operation = $this->createOperationWithMockResponses([
            new RequestException('Network error', new Request('POST', 'test')),
        ], $history);

        $checkout = (new CheckoutParams())
            ->amount(50.0)
            ->productType(ProductType::PAYMENT_GATEWAY)
            ->terminal(new Terminal(1))
            ->orderId(OrderId::fromString(self::ORDER_ID))
        ;

        $this->expectException(WipopApiException::class);
        $this->expectExceptionMessage('Error calling POST');

        try {
            $operation->create($checkout);
        } finally {
            $this->assertCount(1, $history);
        }
    }

    #[Test]
    public function itThrowsSpecificExceptionWhenApiReturnsErrorCode(): void
    {
        $history = [];
        $operation = $this->createOperationWithMockResponses([
            $this->successResponse([
                'status' => 'FAIL',
                'response_code' => [
                    'code' => ApiErrorCode::BC000,
                    'message' => 'Business error',
                ],
            ]),
        ], $history);

        $checkout = (new CheckoutParams())
            ->amount(75.0)
            ->productType(ProductType::PAYMENT_GATEWAY)
            ->terminal(new Terminal(1))
            ->orderId(OrderId::fromString(self::ORDER_ID))
        ;

        $this->expectException(WipopApiBusinessException::class);
        $this->expectExceptionMessage('Business error');

        try {
            $operation->create($checkout);
        } finally {
            $this->assertCount(1, $history);
        }
    }

    #[Test]
    public function itSupportsCheckoutParamsWithAllOptions(): void
    {
        $history = [];
        $operation = $this->createOperationWithMockResponses([$this->successResponse()], $history);

        $params = (new CheckoutParams())
            ->amount(200.0)
            ->currency(Currency::EUR)
            ->productType(ProductType::PAYMENT_GATEWAY)
            ->origin(Origin::API)
            ->terminal(new Terminal(1))
            ->sendEmail(true)
            ->capture(true)
            ->orderId(OrderId::fromString(self::ORDER_ID))
        ;

        $operation->create($params);

        $this->assertCheckoutRequest(
            $history,
            '/k/v1/' . self::MERCHANT_ID . '/checkouts',
            [
                'amount' => 200.0,
                'currency' => Currency::EUR,
                'origin' => Origin::API,
                'product_type' => ProductType::PAYMENT_GATEWAY,
                'terminal' => ['id' => 1],
                'send_email' => true,
                'capture' => true,
                'language' => Language::SPANISH,
                'order_id' => self::ORDER_ID,
            ]
        );
    }

    #[Test]
    public function itThrowsExceptionWhenApiReturnsInvalidJson(): void
    {
        $history = [];
        $operation = $this->createOperationWithMockResponses([
            new Response(200, ['Content-Type' => 'application/json'], '{invalid_json_no_closure'),
        ], $history);

        $checkout = (new CheckoutParams())
            ->amount(80.0)
            ->productType(ProductType::PAYMENT_GATEWAY)
            ->terminal(new Terminal(1))
            ->orderId(OrderId::fromString(self::ORDER_ID))
        ;

        $this->expectException(WipopApiException::class);
        $this->expectExceptionMessage('Error decoding JSON response');

        try {
            $operation->create($checkout);
        } finally {
            $this->assertCount(1, $history);
        }
    }

    #[Test]
    public function itIgnoresUnknownFieldsInCheckoutResponse(): void
    {
        $history = [];
        $operation = $this->createOperationWithMockResponses([
            $this->successResponse([
                'currency' => Currency::EUR,
                'customer_id' => 'cust_123',
                'extra_key' => 'should_be_ignored',
            ]),
        ], $history);

        $checkout = (new CheckoutParams())
            ->amount(120.0)
            ->productType(ProductType::PAYMENT_GATEWAY)
            ->terminal(new Terminal(1))
            ->orderId(OrderId::fromString(self::ORDER_ID))
        ;

        $response = $operation->create($checkout);

        $this->assertSame(ChargeStatus::AVAILABLE, $response->status);
        $this->assertSame('123456', $response->id);
    }

    private function assertCheckoutRequest(array $history, string $expectedPath, array $expectedPayload): void
    {
        $this->assertCount(1, $history);

        $request = $history[0]['request'];
        $this->assertSame('POST', $request->getMethod());
        $this->assertSame($expectedPath, $request->getUri()->getPath());

        $body = (string) $request->getBody();

        try {
            $payload = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            $this->fail('Request body is not valid JSON: ' . $exception->getMessage());
        }

        foreach ($expectedPayload as $key => $value) {
            $this->assertArrayHasKey($key, $payload);

            if (is_float($value)) {
                $this->assertIsNumeric($payload[$key], sprintf('Request payload key "%s" is not numeric.', $key));
                $this->assertEqualsWithDelta(
                    $value,
                    (float) $payload[$key],
                    0.0,
                    sprintf('Failed asserting request payload key "%s" equlas in value', $key)
                );
                continue;
            }

            $this->assertSame($value, $payload[$key], sprintf('Failed to assert request payload key "%s".', $key));
        }
    }

    private function successResponse(array $overrides = []): Response
    {
        $payload = [
            'id' => '123456',
            'amount' => 100.0,
            'status' => ChargeStatus::AVAILABLE,
            'checkout_link' => 'https://checkout.example/123456',
            'creation_date' => '2024-01-01T00:00:00+00:00',
            'expiration_date' => '2024-01-02T00:00:00+00:00',
            'order_id' => self::ORDER_ID,
            'description' => 'Test checkout',
            'customer' => null,
        ];

        $payload = array_merge($payload, $overrides);

        return new Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode($payload, JSON_THROW_ON_ERROR)
        );
    }

    private function createOperationWithMockResponses(array $responses, array &$history): CheckoutOperation
    {
        $mock = new MockHandler($responses);
        $handlerStack = HandlerStack::create($mock);

        $history = [];
        $handlerStack->push(Middleware::history($history));

        $configuration = new ClientConfiguration(
            Environment::SANDBOX,
            self::MERCHANT_ID,
            'sk_test_secret'
        );

        $httpClient = new GuzzleHttpClient(
            $configuration,
            [
                'handler' => $handlerStack,
            ]
        );

        return new CheckoutOperation($httpClient, $configuration);
    }

    private function assertCheckoutResponse(CheckoutResult $response, bool $expectsCustomer = true): void
    {
        $this->assertSame('123456', $response->id);
        $this->assertSame(100.0, $response->amount);
        $this->assertSame('Test checkout', $response->description);
        $this->assertSame(self::ORDER_ID, $response->orderId);
        $this->assertSame(ChargeStatus::AVAILABLE, $response->status);
        $this->assertSame('https://checkout.example/123456', $response->checkoutLink);

        if ($expectsCustomer) {
            $this->assertNotNull($response->customer);
        } else {
            $this->assertNull($response->customer);
        }
    }
}
