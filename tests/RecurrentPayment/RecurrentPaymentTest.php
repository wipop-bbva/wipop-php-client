<?php declare(strict_types=1);

namespace RecurrentPayment;

use PHPUnit\Framework\Attributes\CoversClass;
use Wipop\CardPayment\OriginChannel;
use Wipop\RecurrentPayment\RecurrentPayment;
use PHPUnit\Framework\TestCase;
use Wipop\Utils\OrderId;
use Wipop\Utils\PaymentMethod;
use Wipop\Utils\ProductType;

#[CoversClass(RecurrentPayment::class)]
class RecurrentPaymentTest extends TestCase
{
    public function testItShouldAssignPostTypeRecurrentOnConstructorByDefault(): void
    {
        $recurrentPayment = new RecurrentPayment(
            1,
            PaymentMethod::CARD,
            new OrderId('1234abcdefgh')
        );

        $this->assertEquals('RECURRENT', $recurrentPayment->getPostType());
    }

    public function testItShouldAssignOriginChannelApiOnConstructorByDefault(): void
    {
        $recurrentPayment = new RecurrentPayment(
            1,
            PaymentMethod::CARD,
            new OrderId('1234abcdefgh')
        );

        $this->assertEquals(OriginChannel::API, $recurrentPayment->getOriginChannel());
    }

    public function testItShouldAssignProductTypePaymentGatewayOnConstructorByDefault(): void
    {
        $recurrentPayment = new RecurrentPayment(
            1,
            PaymentMethod::CARD,
            new OrderId('1234abcdefgh')
        );

        $this->assertEquals(ProductType::PAYMENT_GATEWAY, $recurrentPayment->getProductType());
    }

    public function testItShouldAssignTokenizeTrueOnConstructorByDefault(): void
    {
        $recurrentPayment = new RecurrentPayment(
            1,
            PaymentMethod::CARD,
            new OrderId('1234abcdefgh')
        );

        $this->assertTrue($recurrentPayment->isTokenize());
    }

    public function testItShouldAssignTerminalZeroOnConstructorByDefault(): void
    {
        $recurrentPayment = new RecurrentPayment(
            1,
            PaymentMethod::CARD,
            new OrderId('1234abcdefgh')
        );

        $this->assertEquals(0, $recurrentPayment->getTerminal()->getId());
    }
}
