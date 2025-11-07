<?php

declare(strict_types=1);

namespace Wipop\Tests\Charge;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Wipop\CardPayment\Card;
use Wipop\Charge\ChargeMethod;
use Wipop\Charge\ChargeParams;
use Wipop\Charge\OriginChannel;
use Wipop\Charge\PostType;
use Wipop\Charge\PostTypeMode;
use Wipop\Customer\Customer;
use Wipop\Utils\Currency;
use Wipop\Utils\Language;
use Wipop\Utils\OrderId;
use Wipop\Utils\ProductType;
use Wipop\Utils\Terminal;

/**
 * @internal
 */
#[CoversClass(ChargeParams::class)]
class ChargeParamsTest extends TestCase
{
    #[Test]
    public function itBuildsChargePayloadWithDefaults(): void
    {
        $params = (new ChargeParams())
            ->amount(100.0)
            ->method(ChargeMethod::CARD)
            ->currency(Currency::EUR)
            ->productType(ProductType::PAYMENT_LINK)
            ->originChannel(OriginChannel::API)
            ->description('Cargo de prueba')
            ->orderId(OrderId::fromString('1234ABCDEFGH'))
            ->redirectUrl('https://example.com')
            ->sendEmail(false)
            ->terminal(new Terminal(1))
            ->language(Language::SPANISH)
        ;

        $payload = $params->toArray();

        $this->assertSame(
            [
                'amount' => 100.0,
                'method' => ChargeMethod::CARD,
                'description' => 'Cargo de prueba',
                'send_email' => false,
                'currency' => Currency::EUR,
                'origin_channel' => OriginChannel::API,
                'product_type' => ProductType::PAYMENT_LINK,
                'terminal' => ['id' => 1],
                'redirect_url' => 'https://example.com',
                'order_id' => '1234ABCDEFGH',
                'language' => Language::SPANISH,
                'capture' => true,
            ],
            $payload
        );
    }

    #[Test]
    public function itIncludesCustomerDetailsWhenProvided(): void
    {
        $customer = new Customer(
            name: 'Ana',
            lastName: 'García',
            email: 'ana.garcia@example.com',
            publicId: 'ext999',
            externalId: '123456',
            phoneNumber: '+34611111111'
        );

        $params = (new ChargeParams())
            ->amount(55.5)
            ->method(ChargeMethod::CARD)
            ->terminal(new Terminal(1))
            ->orderId(OrderId::fromString('1234ABCDEFGH'))
            ->customer($customer)
            ->sendEmail(true)
            ->language('es')
            ->originChannel(OriginChannel::PAYMENT_LINK)
            ->productType(ProductType::PAYMENT_GATEWAY)
            ->capture(true)
        ;

        $payload = $params->toArray();

        $this->assertTrue($payload['send_email']);
        $this->assertSame('es', $payload['language']);
        $this->assertSame(ChargeMethod::CARD, $payload['method']);
        $this->assertTrue($payload['capture']);
        $this->assertSame(
            [
                'name' => 'Ana',
                'last_name' => 'García',
                'email' => 'ana.garcia@example.com',
                'external_id' => '123456',
                'phone_number' => '+34611111111',
            ],
            $payload['customer']
        );
    }

    #[Test]
    public function itBuildsGatewayPayloadWithCardData(): void
    {
        $card = new Card(
            cardNumber: '4111111111111111',
            expirationYear: '27',
            expirationMonth: '12',
            holderName: 'Carlos López',
            cvv2: '123',
        );

        $params = (new ChargeParams())
            ->amount(100.0)
            ->method(ChargeMethod::CARD)
            ->productType(ProductType::PAYMENT_GATEWAY)
            ->originChannel(OriginChannel::API)
            ->terminal(new Terminal(1))
            ->currency(Currency::EUR)
            ->description('Cargo directo en pasarela')
            ->orderId(OrderId::fromString('1234ABCDEFGH'))
            ->card($card)
        ;

        $payload = $params->toArray();

        $this->assertSame(ProductType::PAYMENT_GATEWAY, $payload['product_type']);
        $this->assertSame('Cargo directo en pasarela', $payload['description']);
        $this->assertSame([
            'card_number' => '4111111111111111',
            'holder_name' => 'Carlos López',
            'expiration_year' => '27',
            'expiration_month' => '12',
            'cvv2' => '123',
        ], $payload['card']);
        $this->assertTrue($payload['capture']);
    }

    #[Test]
    public function itBuildsTokenizationPayload(): void
    {
        $params = (new ChargeParams())
            ->amount(0.0)
            ->method(ChargeMethod::CARD)
            ->productType(ProductType::PAYMENT_LINK)
            ->originChannel(OriginChannel::API)
            ->terminal(new Terminal(1))
            ->orderId(OrderId::fromString('1234ABCDEFGH'))
            ->useCof(true)
            ->description('Tokenize card')
        ;

        $payload = $params->toArray();

        $this->assertSame(0.0, $payload['amount']);
        $this->assertTrue($payload['use_cof']);
        $this->assertTrue($payload['capture']);
    }

    #[Test]
    public function itBuildsGatewayPayloadWithToken(): void
    {
        $params = (new ChargeParams())
            ->amount(35.0)
            ->method(ChargeMethod::CARD)
            ->productType(ProductType::PAYMENT_GATEWAY)
            ->originChannel(OriginChannel::API)
            ->terminal(new Terminal(1))
            ->orderId(OrderId::fromString('1234ABCDEFGH'))
            ->sourceId('card_tok_12345')
            ->useCof(true)
            ->postType(new PostType(PostTypeMode::RECURRENT))
            ->capture(false)
            ->description('MIT recurring charge')
        ;

        $payload = $params->toArray();

        $this->assertSame('card_tok_12345', $payload['source_id']);
        $this->assertTrue($payload['use_cof']);
        $this->assertSame(['mode' => PostTypeMode::RECURRENT->value], $payload['post_type']);
        $this->assertFalse($payload['capture']);
    }

    #[Test]
    public function itFormatsDueDateWhenProvided(): void
    {
        $dueDate = new DateTimeImmutable('2025-12-31 23:59:00');

        $payload = (new ChargeParams())
            ->amount(10.0)
            ->method(ChargeMethod::CARD)
            ->productType(ProductType::PAYMENT_LINK)
            ->originChannel(OriginChannel::API)
            ->terminal(new Terminal(1))
            ->orderId(OrderId::fromString('1234ABCDEFGH'))
            ->dueDate($dueDate)
            ->toArray()
        ;

        $this->assertSame('2025-12-31 23:59:00', $payload['due_date'] ?? null);
    }
}
