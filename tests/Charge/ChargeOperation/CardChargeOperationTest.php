<?php

declare(strict_types=1);

namespace Wipop\Tests\Charge\ChargeOperation;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Wipop\CardPayment\Card;
use Wipop\Charge\ChargeMethod;
use Wipop\Charge\ChargeOperation;
use Wipop\Charge\ChargeParams;
use Wipop\Charge\OriginChannel;
use Wipop\Utils\Currency;
use Wipop\Utils\ProductType;
use Wipop\Utils\Terminal;

/**
 * @internal
 */
#[CoversClass(ChargeOperation::class)]
final class CardChargeOperationTest extends AbstractChargeOperationTestCase
{
    #[Test]
    public function itCreatesHostedCardChargeWithoutCustomer(): void
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
            ->setCard(new Card(
                cardNumber: '4111111111111111',
                expirationYear: '27',
                expirationMonth: '12',
                holderName: 'Carlos López',
                cvv2: '123',
            ))
        ;

        $operation->create($params);

        /** @var array<int, array{request: RequestInterface, response: null|ResponseInterface, error: mixed, options: array<string, mixed>}> $history */
        $payload = $this->decodeRequestBody($history[0]['request']);

        $this->assertSame('Direct gateway charge', $payload['description']);
        $this->assertSame(ProductType::PAYMENT_GATEWAY, $payload['product_type']);
        $this->assertSame([
            'card_number' => '4111111111111111',
            'holder_name' => 'Carlos López',
            'expiration_year' => '27',
            'expiration_month' => '12',
            'cvv2' => '123',
        ], $payload['card']);
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
}
