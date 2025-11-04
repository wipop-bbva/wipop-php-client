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
use Wipop\Charge\PostType;
use Wipop\Charge\PostTypeMode;
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
            ->setCurrency(Currency::EUR)
            ->setDescription('Charge via payment link')
            ->setOriginChannel(OriginChannel::API)
            ->setRedirectUrl('https://miapp.com/return')
            ->setLanguage('es')
            ->setSendEmail(false)
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
                'description' => 'Charge via payment link',
                'redirect_url' => 'https://miapp.com/return',
                'language' => 'es',
                'capture' => true,
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
            ->setDescription('Compra test Bizum')
            ->setOriginChannel(OriginChannel::API)
            ->setRedirectUrl('https://miweb.com/callback')
            ->setLanguage('es')
            ->setCustomer($customer)
        ;

        $operation->create($params);

        $this->assertRequest(
            $history[0]['request'],
            '/b/v1/' . self::MERCHANT_ID . '/customers/cust_123/charges',
            [
                'amount' => 45.0,
                'method' => ChargeMethod::BIZUM,
                'currency' => Currency::EUR,
                'product_type' => ProductType::PAYMENT_LINK,
                'origin_channel' => OriginChannel::API,
                'description' => 'Compra test Bizum',
                'redirect_url' => 'https://miweb.com/callback',
                'language' => 'es',
                'send_email' => false,
                'capture' => true,
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

    #[Test]
    public function itCreatesGatewayChargeWithCardPayload(): void
    {
        $history = [];
        $operation = $this->createOperationWithMockResponses([$this->successResponse()], $history);

        $params = (new ChargeParams())
            ->setAmount(100.0)
            ->setMethod(ChargeMethod::CARD)
            ->setProductType(ProductType::PAYMENT_GATEWAY)
            ->setOriginChannel(OriginChannel::API)
            ->setTerminal(new Terminal(1))
            ->setCurrency(Currency::EUR)
            ->setDescription('Direct gateway charge')
            ->setCardPayload([
                'card_number' => '4111111111111111',
                'holder_name' => 'Ana García',
                'expiration_year' => '27',
                'expiration_month' => '12',
                'cvv2' => '123',
            ])
        ;

        $operation->create($params);

        $requestPayload = $this->decodeRequestBody($history[0]['request']);

        $this->assertSame('Direct gateway charge', $requestPayload['description']);
        $this->assertSame(ProductType::PAYMENT_GATEWAY, $requestPayload['product_type']);
        $this->assertSame([
            'card_number' => '4111111111111111',
            'holder_name' => 'Ana García',
            'expiration_year' => '27',
            'expiration_month' => '12',
            'cvv2' => '123',
        ], $requestPayload['card']);
    }

    #[Test]
    public function itTokenizesCardAndUsesTokenForGatewayCharge(): void
    {
        $history = [];
        $operation = $this->createOperationWithMockResponses([
            $this->successResponse(),
            $this->successResponse(),
        ], $history);

        $tokenizationParams = (new ChargeParams())
            ->setAmount(0.0)
            ->setMethod(ChargeMethod::CARD)
            ->setProductType(ProductType::PAYMENT_LINK)
            ->setOriginChannel(OriginChannel::API)
            ->setTerminal(new Terminal(1))
            ->setUseCof(true)
            ->setDescription('Tokenize card via hosted form')
        ;

        $operation->create($tokenizationParams);

        $token = 'card_token_6789';

        $gatewayParams = (new ChargeParams())
            ->setAmount(35.0)
            ->setMethod(ChargeMethod::CARD)
            ->setProductType(ProductType::PAYMENT_GATEWAY)
            ->setOriginChannel(OriginChannel::API)
            ->setTerminal(new Terminal(1))
            ->setSourceId($token)
            ->setUseCof(true)
            ->setPostType(new PostType(PostTypeMode::RECURRENT))
            ->setCapture(false)
            ->setDescription('MIT recurring charge')
        ;

        $operation->create($gatewayParams);

        $firstPayload = $this->decodeRequestBody($history[0]['request']);
        $this->assertSame(0.0, (float) $firstPayload['amount']);
        $this->assertTrue($firstPayload['use_cof']);
        $this->assertTrue($firstPayload['capture']);
        $this->assertSame(ProductType::PAYMENT_LINK, $firstPayload['product_type']);

        $secondPayload = $this->decodeRequestBody($history[1]['request']);
        $this->assertSame($token, $secondPayload['source_id']);
        $this->assertTrue($secondPayload['use_cof']);
        $this->assertSame(['mode' => PostTypeMode::RECURRENT->value], $secondPayload['post_type']);
        $this->assertFalse($secondPayload['capture']);
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

        $body = $this->decodeRequestBody($request);

        foreach ($expectedFragment as $key => $value) {
            $this->assertArrayHasKey($key, $body);
            $this->assertEquals($value, $body[$key]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeRequestBody(RequestInterface $request): array
    {
        /** @var array<string, mixed> $body */
        $body = json_decode((string) $request->getBody(), true, 512, JSON_THROW_ON_ERROR);

        return $body;
    }
}
