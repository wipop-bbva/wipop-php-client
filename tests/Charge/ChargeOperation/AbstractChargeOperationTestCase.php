<?php

declare(strict_types=1);

namespace Wipop\Tests\Charge\ChargeOperation;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Wipop\Charge\ChargeOperation;
use Wipop\Client\ClientConfiguration;
use Wipop\Client\Environment;
use Wipop\Client\Http\GuzzleHttpClient;

use const JSON_THROW_ON_ERROR;

abstract class AbstractChargeOperationTestCase extends TestCase
{
    protected const MERCHANT_ID = 'm1234567890123456789';
    protected const ORDER_ID = '1234ABCDEFGH';

    protected function createOperationWithMockResponses(array $responses, array &$history): ChargeOperation
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

        return new ChargeOperation($httpClient, $configuration);
    }

    protected function successResponse(array $overrides = []): Response
    {
        $payload = [
            'status' => 'CHARGE_PENDING',
            'id' => 'txn_123',
            'amount' => '100.50',
            'currency' => 'EUR',
            'method' => 'CARD',
            'description' => 'Test charge',
            'order_id' => '1234ABCDEFGH',
            'customer_id' => 'cust_123',
            'customer' => ['public_id' => 'cust_123', 'email' => 'ana@example.com'],
            'creation_date' => '2025-01-01T10:00:00+00:00',
            'operation_date' => '2025-01-01T10:05:00+00:00',
            'transaction_type' => 'CHARGE',
            'operation_type' => 'IN',
            'terminal' => ['id' => 1],
            'payment_method' => ['url' => 'https://pay.example/wipop', 'type' => 'REDIRECT'],
            'card' => [
                'id' => 'card_tok_001',
                'masked' => '411111******1111',
                'last_digits' => '1111',
                'expiration_month' => '12',
                'expiration_year' => '27',
            ],
            'authorization' => 'AUTH123',
            'use_cof' => false,
            'refund' => null,
            'metadata' => ['key' => 'value'],
            'error_code' => '123',
            'error_message' => null,
            'ignored_field' => 'extra',
        ];

        if ($overrides !== []) {
            $payload = array_replace_recursive($payload, $overrides);
        }

        return new Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode($payload, JSON_THROW_ON_ERROR)
        );
    }

    protected function assertRequest(RequestInterface $request, string $expectedPath, array $expectedFragment): void
    {
        $this->assertSame('POST', $request->getMethod());
        $this->assertSame($expectedPath, $request->getUri()->getPath());

        $body = $this->decodeRequestBody($request);

        foreach ($expectedFragment as $key => $value) {
            $this->assertArrayHasKey($key, $body);
            $this->assertEquals($value, $body[$key]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function decodeRequestBody(RequestInterface $request): array
    {
        // @var array<string, mixed> $body
        return json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR);
    }
}
