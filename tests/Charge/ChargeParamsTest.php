<?php

declare(strict_types=1);

namespace Wipop\Tests\Charge;

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
            ->setAmount(100.0)
            ->setMethod(ChargeMethod::CARD)
            ->setCurrency(Currency::EUR)
            ->setProductType(ProductType::PAYMENT_LINK)
            ->setOriginChannel(OriginChannel::API)
            ->setDescription('Cargo de prueba')
            ->setOrderId(OrderId::fromString('1234ABCDEFGH'))
            ->setRedirectUrl('https://example.com')
            ->setSendEmail(false)
            ->setTerminal(new Terminal(1))
            ->setLanguage(Language::SPANISH)
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
            ->setAmount(55.5)
            ->setMethod(ChargeMethod::CARD)
            ->setTerminal(new Terminal(1))
            ->setCustomer($customer)
            ->setSendEmail(true)
            ->setLanguage('es')
            ->setOriginChannel(OriginChannel::PAYMENT_LINK)
            ->setProductType(ProductType::PAYMENT_GATEWAY)
            ->setCapture(true)
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
            ->setAmount(100.0)
            ->setMethod(ChargeMethod::CARD)
            ->setProductType(ProductType::PAYMENT_GATEWAY)
            ->setOriginChannel(OriginChannel::API)
            ->setTerminal(new Terminal(1))
            ->setCurrency(Currency::EUR)
            ->setDescription('Cargo directo en pasarela')
            ->setCard($card)
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
            ->setAmount(0.0)
            ->setMethod(ChargeMethod::CARD)
            ->setProductType(ProductType::PAYMENT_LINK)
            ->setOriginChannel(OriginChannel::API)
            ->setTerminal(new Terminal(1))
            ->setUseCof(true)
            ->setDescription('Tokenize card')
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
            ->setAmount(35.0)
            ->setMethod(ChargeMethod::CARD)
            ->setProductType(ProductType::PAYMENT_GATEWAY)
            ->setOriginChannel(OriginChannel::API)
            ->setTerminal(new Terminal(1))
            ->setSourceId('card_tok_12345')
            ->setUseCof(true)
            ->setPostType(new PostType(PostTypeMode::RECURRENT))
            ->setCapture(false)
            ->setDescription('MIT recurring charge')
        ;

        $payload = $params->toArray();

        $this->assertSame('card_tok_12345', $payload['source_id']);
        $this->assertTrue($payload['use_cof']);
        $this->assertSame(['mode' => PostTypeMode::RECURRENT->value], $payload['post_type']);
        $this->assertFalse($payload['capture']);
    }
}
