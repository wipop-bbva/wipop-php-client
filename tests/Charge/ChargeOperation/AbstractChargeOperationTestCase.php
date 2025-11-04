<?php

declare(strict_types=1);

namespace Wipop\Tests\Charge\ChargeOperation;

use GuzzleHttp\Client;
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

    protected function createOperationWithMockResponses(array $responses, array &$history): ChargeOperation
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

    protected function successResponse(): Response
    {
        return new Response(
            200,
            ['Content-Type' => 'application/json'],
            '{"status":"SUCCESS"}'
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
