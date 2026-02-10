<?php

declare(strict_types=1);

namespace Wipop\Tests\Charge\ChargeOperation;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Wipop\Domain\Charge;
use Wipop\Domain\ChargeMethod;
use Wipop\Domain\Currency;
use Wipop\Domain\Input\Card;
use Wipop\Domain\OriginChannel;
use Wipop\Domain\PaymentMethodType;
use Wipop\Domain\ProductType;
use Wipop\Domain\TransactionStatus;
use Wipop\Domain\Value\OrderId;
use Wipop\Domain\Value\Terminal;
use Wipop\Operations\Charge\ChargeOperation;
use Wipop\Operations\Charge\Params\CreateChargeParams;

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

        $params = (new CreateChargeParams())
            ->amount(100.0)
            ->method(ChargeMethod::CARD)
            ->terminal(new Terminal(1))
            ->productType(ProductType::PAYMENT_LINK)
            ->currency(Currency::EUR)
            ->description('Charge via payment link')
            ->originChannel(OriginChannel::API)
            ->redirectUrl('https://miapp.com/return')
            ->language('es')
            ->sendEmail(false)
            ->orderId(OrderId::fromString(self::ORDER_ID))
        ;

        $response = $operation->create($params);
        $this->assertInstanceOf(Charge::class, $response);
        $this->assertSame(TransactionStatus::CHARGE_PENDING, $response->status);
        $this->assertSame('txn_123', $response->id);
        $this->assertSame(ChargeMethod::CARD, $response->method);
        $this->assertSame(100.50, $response->amount);
        $this->assertSame(Currency::EUR, $response->currency);
        $this->assertSame('CHARGE', $response->transactionType);
        $this->assertSame('IN', $response->operationType);
        $this->assertSame('cust_123', $response->customerId);
        $this->assertNotNull($response->paymentMethod);
        $this->assertSame('https://pay.example/wipop', $response->paymentMethod?->url);
        $this->assertSame(PaymentMethodType::REDIRECT, $response->paymentMethod?->type);
        $this->assertSame('card_tok_001', $response->card?->id);

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

        $params = (new CreateChargeParams())
            ->amount(100.0)
            ->method(ChargeMethod::CARD)
            ->productType(ProductType::PAYMENT_GATEWAY)
            ->originChannel(OriginChannel::API)
            ->terminal(new Terminal(1))
            ->currency(Currency::EUR)
            ->description('Direct gateway charge')
            ->orderId(OrderId::fromString(self::ORDER_ID))
            ->card(new Card(
                cardNumber: '4111111111111111',
                expirationYear: '27',
                expirationMonth: '12',
                holderName: 'Carlos López',
                cvv2: '123',
            ))
        ;

        $response = $operation->create($params);
        $this->assertInstanceOf(Charge::class, $response);
        $this->assertSame(TransactionStatus::CHARGE_PENDING, $response->status);

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

        $params = (new CreateChargeParams())->amount(10.0);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Charge terminal is required.');

        $operation->create($params);
    }
}
