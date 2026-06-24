<?php

declare(strict_types=1);

namespace Wipop\Tests\Serializer;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Wipop\Domain\Address;
use Wipop\Domain\Card;
use Wipop\Domain\Charge;
use Wipop\Domain\Customer;
use Wipop\Domain\PaymentMethod;
use Wipop\Domain\PaymentMethodType;
use Wipop\Domain\Refund;
use Wipop\Domain\Terminal;
use Wipop\Domain\TransactionStatus;
use Wipop\Serializer\Attribute\SerializedName;
use Wipop\Serializer\Hydrator;

/**
 * @internal
 */
#[CoversClass(Hydrator::class)]
#[CoversClass(SerializedName::class)]
final class HydratorTest extends TestCase
{
    #[Test]
    public function itHydratesSdkModelsWithoutSymfonySerializer(): void
    {
        $charge = (new Hydrator())->hydrate(Charge::class, [
            'id' => 'txn_123',
            'method' => 'CARD',
            'amount' => '12.34',
            'creation_date' => '2026-06-18T10:00:00+00:00',
            'operation_date' => '2026-06-18T10:01:00+00:00',
            'status' => 'COMPLETED',
            'transaction_type' => 'CAPTURE',
            'operation_type' => 'IN',
            'order_id' => 'ord_123',
            'customer_id' => 'customer_123',
            'use_cof' => true,
            'payment_method' => [
                'type' => 'REDIRECT',
                'url' => 'https://example.test/pay',
            ],
            'terminal' => [
                'id' => '1',
            ],
            'customer' => [
                'name' => 'Ana',
                'last_name' => 'García',
                'email' => 'ana@example.test',
                'phone_number' => '+34611111111',
                'address' => [
                    'line1' => 'Main street',
                    'postal_code' => '28001',
                    'country_code' => 'ES',
                ],
            ],
            'card' => [
                'id' => 'card_123',
                'holder_name' => 'Ana García',
                'expiration_month' => '12',
                'expiration_year' => '30',
                'card_number' => '************1111',
                'bank_name' => 'Wipop Bank',
                'bank_code' => '9999',
            ],
            'refund' => [
                'id' => 'refund_123',
                'amount' => '2.34',
                'status' => 'COMPLETED',
            ],
            'metadata' => [
                'cart_id' => 10,
            ],
            'ignored_field' => 'ignored',
        ]);

        self::assertInstanceOf(Charge::class, $charge);
        self::assertSame('txn_123', $charge->id);
        self::assertSame(12.34, $charge->amount);
        self::assertInstanceOf(DateTimeImmutable::class, $charge->creationDate);
        self::assertSame(TransactionStatus::COMPLETED, $charge->status);
        self::assertSame('CAPTURE', $charge->transactionType);
        self::assertTrue($charge->useCof);
        self::assertInstanceOf(PaymentMethod::class, $charge->paymentMethod);
        self::assertSame(PaymentMethodType::REDIRECT, $charge->paymentMethod->type);
        self::assertSame('https://example.test/pay', $charge->paymentMethod->url);
        self::assertInstanceOf(Terminal::class, $charge->terminal);
        self::assertSame(1, $charge->terminal->id);
        self::assertInstanceOf(Customer::class, $charge->customer);
        self::assertSame('Ana', $charge->customer->name);
        self::assertSame('García', $charge->customer->lastName);
        self::assertInstanceOf(Address::class, $charge->customer->address);
        self::assertSame('28001', $charge->customer->address->postalCode);
        self::assertSame('ES', $charge->customer->address->countryCode);
        self::assertInstanceOf(Card::class, $charge->card);
        self::assertSame('Ana García', $charge->card->holderName);
        self::assertSame('12', $charge->card->expirationMonth);
        self::assertSame('************1111', $charge->card->cardNumber);
        self::assertSame('Wipop Bank', $charge->card->bankName);
        self::assertInstanceOf(Refund::class, $charge->refund);
        self::assertSame(2.34, $charge->refund->amount);
        self::assertSame(['cart_id' => 10], $charge->metadata);
        self::assertFalse(class_exists('Symfony\Component\Serializer\Serializer', false));
    }

    #[Test]
    public function itHydratesBooleanStringValues(): void
    {
        $charge = (new Hydrator())->hydrate(Charge::class, [
            'use_cof' => 'false',
        ]);

        self::assertFalse($charge->useCof);
    }

    #[Test]
    public function itThrowsWhenBooleanValueIsInvalid(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to hydrate Wipop boolean value');

        (new Hydrator())->hydrate(Charge::class, [
            'use_cof' => 'oops',
        ]);
    }

    #[Test]
    public function itThrowsWhenStringValueIsInvalid(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to hydrate Wipop string value');

        (new Hydrator())->hydrate(Charge::class, [
            'id' => [],
        ]);
    }

    #[Test]
    public function itThrowsWhenFloatValueIsInvalid(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to hydrate Wipop numeric value');

        (new Hydrator())->hydrate(Charge::class, [
            'amount' => 'oops',
        ]);
    }

    #[Test]
    public function itThrowsWhenIntegerValueIsInvalid(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to hydrate Wipop integer value');

        (new Hydrator())->hydrate(Charge::class, [
            'terminal' => [
                'id' => 'abc',
            ],
        ]);
    }

    #[Test]
    public function itThrowsWhenTheRequestedModelDoesNotExist(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to hydrate unknown model');

        // @phpstan-ignore-next-line: intentionally verifies the runtime guard for unknown models.
        (new Hydrator())->hydrate('Wipop\Domain\MissingModel', []);
    }

    #[Test]
    public function itThrowsWhenEnumValueIsUnknown(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to hydrate Wipop enum');

        (new Hydrator())->hydrate(Charge::class, [
            'status' => 'UNKNOWN',
        ]);
    }

    #[Test]
    public function itThrowsWhenDateValueIsInvalid(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to hydrate Wipop date value');

        (new Hydrator())->hydrate(Charge::class, [
            'creation_date' => 'not-a-date',
        ]);
    }
}
