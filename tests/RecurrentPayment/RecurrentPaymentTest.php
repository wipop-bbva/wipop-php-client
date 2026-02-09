<?php

declare(strict_types=1);

namespace Wipop\Tests\RecurrentPayment;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Wipop\Domain\Input\OriginChannel;
use Wipop\Domain\Input\PaymentMethod;
use Wipop\Domain\ProductType;
use Wipop\Domain\Value\OrderId;
use Wipop\Operations\RecurrentPayment\RecurrentPayment;

/**
 * @internal
 */
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
