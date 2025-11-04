<?php

declare(strict_types=1);

namespace Wipop\Tests\Charge;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Wipop\Charge\ChargeMethod;
use Wipop\Charge\ChargeParams;
use Wipop\Charge\OriginChannel;
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
            ->setDescription('Test charge')
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
                'description' => 'Test charge',
                'send_email' => false,
                'currency' => Currency::EUR,
                'origin_channel' => OriginChannel::API,
                'product_type' => ProductType::PAYMENT_LINK,
                'terminal' => ['id' => 1],
                'redirect_url' => 'https://example.com',
                'order_id' => '1234ABCDEFGH',
                'customer' => [
                    'name' => '',
                    'last_name' => '',
                    'email' => '',
                    'phone_number' => '',
                    'external_id' => '',
                ],
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
            ->setTerminal(new Terminal(2))
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
}
