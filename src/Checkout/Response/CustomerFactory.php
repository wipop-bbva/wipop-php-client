<?php

declare(strict_types=1);

namespace Wipop\Checkout\Response;

use DateTimeImmutable;
use DateTimeInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Wipop\Customer\Customer;
use Wipop\Customer\CustomerInterface;
use Wipop\Customer\NullCustomer;

final class CustomerFactory
{
    private AddressFactory $addressFactory;

    public function __construct(?AddressFactory $addressFactory = null)
    {
        $this->addressFactory = $addressFactory ?? new AddressFactory();
    }

    /**
     * @param null|array<string,mixed> $payload
     */
    public function fromArray(?array $payload): CustomerInterface
    {
        if ($payload === null) {
            return new NullCustomer();
        }

        $data = $this->resolvePayload($payload);
        $creationDate = $data['creation_date'] !== null
            ? new DateTimeImmutable($data['creation_date']) : null;

        $addressPayload = $data['address'];

        return new Customer(
            $data['name'],
            $data['last_name'],
            $data['email'],
            $data['public_id'],
            $data['external_id'],
            $data['phone_number'],
            $this->addressFactory->fromArray($addressPayload),
            $creationDate,
        );
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @return array{
     *     name: string,
     *     last_name: string,
     *     email: string,
     *     public_id: null|string,
     *     external_id: null|string,
     *     phone_number: null|string,
     *     address: null|array<string, mixed>,
     *     creation_date: null|DateTimeInterface|string
     * }
     */
    private function resolvePayload(array $payload): array
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'name' => '',
            'last_name' => '',
            'email' => '',
            'public_id' => null,
            'external_id' => null,
            'phone_number' => null,
            'address' => null,
            'creation_date' => null,
        ]);

        foreach (['name', 'last_name', 'email'] as $key) {
            $resolver->setAllowedTypes($key, ['null', 'string']);
            $resolver->setNormalizer($key, function (Options $options, mixed $value): string {
                return $value ?? '';
            });
        }

        foreach (['public_id', 'external_id', 'phone_number'] as $key) {
            $resolver->setAllowedTypes($key, ['null', 'string']);
        }

        $resolver->setAllowedTypes('address', ['null', 'array']);
        $resolver->setAllowedTypes('creation_date', ['null', 'string']);

        return $resolver->resolve($payload);
    }
}
