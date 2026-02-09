<?php

declare(strict_types=1);

namespace Wipop\Tests\CardPayment;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Wipop\Domain\Input\CardPayment;
use Wipop\Domain\Input\PaymentMethod;
use Wipop\Domain\Value\Terminal;

/**
 * @internal
 */
#[CoversClass(CardPayment::class)]
class CardPaymentTest extends TestCase
{
    #[Test]
    public function itShouldAssignMethodCardOnConstructorByDefault(): void
    {
        $cardPayment = new CardPayment(
            1,
            new Terminal(0)
        );

        $this->assertEquals(PaymentMethod::CARD, $cardPayment->getMethod());
    }
}
