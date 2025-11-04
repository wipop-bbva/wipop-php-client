<?php

declare(strict_types=1);

namespace Wipop\Tests\Checkout\Response;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Wipop\Checkout\Response\AddressFactory;
use Wipop\Checkout\Response\CustomerFactory;
use Wipop\Customer\Customer;
use Wipop\Customer\NullCustomer;

/**
 * @internal
 */
#[CoversClass(CustomerFactory::class)]
class CustomerFactoryTest extends TestCase
{
    #[Test]
    public function itReturnsNullCustomerWhenPayloadIsNull(): void
    {
        $factory = new CustomerFactory();

        $customer = $factory->fromArray(null);

        $this->assertInstanceOf(NullCustomer::class, $customer);
    }

    #[Test]
    public function itBuildsCustomerFromPayload(): void
    {
        $factory = new CustomerFactory(new AddressFactory());

        $customer = $factory->fromArray([
            'name' => 'Ana',
            'last_name' => 'García',
            'email' => 'ana@example.com',
            'public_id' => 'cus_123',
            'external_id' => 'ext_456',
            'phone_number' => '+34123456789',
            'address' => [
                'address' => 'Calle 1',
                'zip_code' => '28001',
                'city' => 'Madrid',
                'state' => 'Madrid',
                'country_code' => 'ES',
            ],
            'creation_date' => '2024-05-01T08:41:08+00:00',
        ]);

        $this->assertInstanceOf(Customer::class, $customer);
        $this->assertSame('Ana', $customer->getName());
        $this->assertSame('García', $customer->getLastName());
        $this->assertSame('ana@example.com', $customer->getEmail());
        $this->assertSame('cus_123', $customer->getPublicId());
        $this->assertSame('ext_456', $customer->getExternalId());
        $this->assertSame('+34123456789', $customer->getPhoneNumber());
        $this->assertSame('Madrid', $customer->getAddress()?->getCity());
        $this->assertSame('2024-05-01T08:41:08+00:00', $customer->getCreationDate()?->format('c'));
    }

    #[Test]
    public function itFailsWhenPayloadContainsInvalidTypes(): void
    {
        $factory = new CustomerFactory();

        $this->expectException(InvalidOptionsException::class);

        $factory->fromArray([
            'name' => 123,
        ]);
    }
}
