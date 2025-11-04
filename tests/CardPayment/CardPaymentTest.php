<?php

declare(strict_types=1);

namespace Wipop\Tests\CardPayment;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Wipop\CardPayment\CardPayment;
use Wipop\Utils\Terminal;

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

        $this->assertEquals('CARD', $cardPayment->getMethod());
    }
}
