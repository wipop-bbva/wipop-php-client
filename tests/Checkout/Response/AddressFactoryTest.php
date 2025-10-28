<?php

declare(strict_types=1);

namespace Wipop\Tests\Checkout\Response;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Wipop\Checkout\Response\AddressFactory;
use Wipop\Customer\Address;

/**
 * @internal
 */
#[CoversClass(AddressFactory::class)]
class AddressFactoryTest extends TestCase
{
    #[Test]
    public function itReturnsNullWhenPayloadIsNull(): void
    {
        $factory = new AddressFactory();

        $address = $factory->fromArray(null);

        $this->assertNull($address);
    }

    #[Test]
    public function itBuildsAddressFromPayload(): void
    {
        $factory = new AddressFactory();

        $address = $factory->fromArray([
            'address' => 'Calle 1',
            'zip_code' => '28001',
            'city' => 'Madrid',
            'state' => 'Madrid',
            'country_code' => 'ES',
        ]);

        $this->assertInstanceOf(Address::class, $address);
        $this->assertSame('Calle 1', $address->getAddress());
        $this->assertSame('ES', $address->getCountryCode());
    }

    #[Test]
    public function itFailsWhenPayloadContainsInvalidTypes(): void
    {
        $factory = new AddressFactory();

        $this->expectException(InvalidOptionsException::class);

        $factory->fromArray([
            'address' => 123,
        ]);
    }
}
