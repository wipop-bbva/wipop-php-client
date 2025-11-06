<?php

declare(strict_types=1);

namespace Wipop\Tests\Checkout;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Wipop\Checkout\CheckoutParams;
use Wipop\Utils\Terminal;

/**
 * @internal
 */
#[CoversClass(CheckoutParams::class)]
final class CheckoutParamsTest extends TestCase
{
    #[Test]
    public function itFormatsExpirationDateAsDateTimeString(): void
    {
        $expirationDate = new DateTimeImmutable('2025-10-31T15:05:06');

        $payload = (new CheckoutParams())
            ->amount(10.0)
            ->terminal(new Terminal(42))
            ->expirationDate($expirationDate)
            ->toArray()
        ;

        $this->assertArrayHasKey('expiration_date', $payload);
        $this->assertSame('2025-10-31 15:05:06', $payload['expiration_date']);
    }
}
