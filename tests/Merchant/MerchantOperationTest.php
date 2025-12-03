<?php

declare(strict_types=1);

namespace Wipop\Tests\Merchant;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Wipop\Client\ClientConfiguration;
use Wipop\Client\Environment;
use Wipop\Client\Http\GuzzleHttpClient;
use Wipop\Merchant\MerchantOperation;
use Wipop\Utils\ProductType;
use Wipop\Utils\Terminal;

use function json_encode;

use const JSON_THROW_ON_ERROR;

/**
 * @internal
 */
#[CoversClass(MerchantOperation::class)]
final class MerchantOperationTest extends TestCase
{
    private const MERCHANT_ID = 'm1234567890123456789';

    #[Test]
    public function itFetchesPaymentMethodsForTerminal(): void
    {
        $history = [];
        $operation = $this->createOperationWithMockResponses(
            [
                new Response(
                    200,
                    ['Content-Type' => 'application/json'],
                    json_encode(['CARD', 'GOOGLE_PAY', 'BIZUM'], JSON_THROW_ON_ERROR)
                ),
            ],
            $history
        );

        $result = $operation->listPaymentMethods(ProductType::PAYMENT_GATEWAY, new Terminal(1));

        $this->assertSame(['CARD', 'GOOGLE_PAY', 'BIZUM'], $result);
        $this->assertCount(1, $history);

        $request = $history[0]['request'];
        $this->assertSame('GET', $request->getMethod());
        $this->assertSame('/m/v1/' . self::MERCHANT_ID . '/payment_methods', $request->getUri()->getPath());
    }

    /**
     * @param Response[]        $responses
     * @param array<int, mixed> &$history
     */
    private function createOperationWithMockResponses(array $responses, array &$history): MerchantOperation
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

        $httpClient = new GuzzleHttpClient($configuration, [
            'handler' => $handlerStack,
        ]);

        return new MerchantOperation($httpClient, $configuration);
    }
}
