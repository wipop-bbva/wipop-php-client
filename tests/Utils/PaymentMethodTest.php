<?php

declare(strict_types=1);

namespace Wipop\Tests\Utils;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Wipop\Domain\Input\PaymentMethod;

/**
 * @internal
 */
#[CoversClass(PaymentMethod::class)]
final class PaymentMethodTest extends TestCase
{
    #[Test]
    public function itShouldDefaultTypeToRedirectWhenTypeIsNotProvided(): void
    {
        $paymentMethod = new PaymentMethod('https://example.com/payment');

        $this->assertSame('https://example.com/payment', $paymentMethod->getUrl());
        $this->assertSame(PaymentMethod::REDIRECT, $paymentMethod->getType());
    }

    #[Test]
    public function itShouldRespectCustomTypeWhenProvided(): void
    {
        $paymentMethod = new PaymentMethod('https://example.com/payment', PaymentMethod::CARD);

        $this->assertSame(PaymentMethod::CARD, $paymentMethod->getType());
    }
}
