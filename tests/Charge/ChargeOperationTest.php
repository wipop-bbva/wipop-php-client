<?php

declare(strict_types=1);

namespace Wipop\Tests\Charge;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Wipop\Charge\ChargeMethod;
use Wipop\Charge\ChargeOperation;
use Wipop\Charge\ChargeParams;
use Wipop\Charge\OriginChannel;
use Wipop\Client\ClientConfiguration;
use Wipop\Client\Environment;
use Wipop\Client\Http\GuzzleHttpClient;
use Wipop\Customer\Customer;
use Wipop\Utils\Currency;
use Wipop\Utils\ProductType;
use Wipop\Utils\Terminal;

use function json_decode;

use const JSON_THROW_ON_ERROR;

/**
 * @internal
 */
#[CoversClass(ChargeOperation::class)]
class ChargeOperationTest extends TestCase
{
    private const MERCHANT_ID = 'm1234567890123456789';

    #[Test]
    public function itCreatesCardChargeWithoutCustomer(): void
    {
        $history = [];
        $operation = $this->createOperationWithMockResponses([$this->successResponse()], $history);

        $params = (new ChargeParams())
            ->setAmount(100.0)
            ->setMethod(ChargeMethod::CARD)
            ->setTerminal(new Terminal(1))
            ->setProductType(ProductType::PAYMENT_LINK)
        ;

        $operation->create($params);

        $this->assertRequest(
            $history[0]['request'],
            '/c/v1/' . self::MERCHANT_ID . '/charges',
            [
                'amount' => 100.0,
                'method' => ChargeMethod::CARD,
                'currency' => Currency::EUR,
                'origin_channel' => OriginChannel::API,
                'product_type' => ProductType::PAYMENT_LINK,
                'send_email' => false,
                'terminal' => ['id' => 1],
            ]
        );
    }

    #[Test]
    public function itCreatesBizumChargeForExistingCustomer(): void
    {
        $history = [];
        $operation = $this->createOperationWithMockResponses([$this->successResponse()], $history);

        $customer = new Customer(
            name: 'Ana',
            lastName: 'García',
            email: 'ana.garcia@example.com',
            publicId: 'cust_123'
        );

        $params = (new ChargeParams())
            ->setAmount(45.0)
            ->setMethod(ChargeMethod::BIZUM)
            ->setTerminal(new Terminal(1))
            ->setProductType(ProductType::PAYMENT_LINK)
            ->setCustomer($customer)
        ;

        $operation->create($params);

        $this->assertRequest(
            $history[0]['request'],
            '/b/v1/' . self::MERCHANT_ID . '/customers/cust_123/charges',
            [
                'amount' => 45.0,
                'method' => ChargeMethod::BIZUM,
                'product_type' => ProductType::PAYMENT_LINK,
                'origin_channel' => OriginChannel::API,
                'terminal' => ['id' => 1],
            ]
        );
    }

    #[Test]
    public function itRequiresTerminalToBeSet(): void
    {
        $history = [];
        $operation = $this->createOperationWithMockResponses([$this->successResponse()], $history);

        $params = (new ChargeParams())
            ->setAmount(10.0)
        ;

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Charge terminal is required.');

        $operation->create($params);
    }

    /** @param array<int, Response> $responses */
    private function createOperationWithMockResponses(array $responses, array &$history): ChargeOperation
    {
        $mock = new MockHandler($responses);
        $handlerStack = HandlerStack::create($mock);

        $history = [];
        $handlerStack->push(Middleware::history($history));

        $client = new Client([
            'handler' => $handlerStack,
            'base_uri' => ClientConfiguration::SANDBOX_API_URL,
        ]);

        $configuration = new ClientConfiguration(
            Environment::SANDBOX,
            self::MERCHANT_ID,
            'sk_test_secret'
        );

        return new ChargeOperation(new GuzzleHttpClient($client), $configuration);
    }

    private function successResponse(): Response
    {
        return new Response(
            200,
            ['Content-Type' => 'application/json'],
            '{"status":"SUCCESS"}'
        );
    }

    /**
     * @param array<string, mixed> $expectedFragment
     */
    private function assertRequest(RequestInterface $request, string $expectedPath, array $expectedFragment): void
    {
        $this->assertSame('POST', $request->getMethod());
        $this->assertSame($expectedPath, $request->getUri()->getPath());

        /** @var array<string, mixed> $body */
        $body = json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR);

        foreach ($expectedFragment as $key => $value) {
            $this->assertArrayHasKey($key, $body);
            $this->assertEquals($value, $body[$key]);
        }
    }
}
