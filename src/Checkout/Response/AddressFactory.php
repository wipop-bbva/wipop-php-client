<?php

declare(strict_types=1);

namespace Wipop\Checkout\Response;

use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Wipop\Customer\Address;

final class AddressFactory
{
    /**
     * @param null|array<string,mixed> $payload
     */
    public function fromArray(?array $payload): ?Address
    {
        if ($payload === null) {
            return null;
        }

        $data = $this->resolvePayload($payload);

        return new Address(
            $data['address'],
            $data['zip_code'],
            $data['city'],
            $data['state'],
            $data['country_code']
        );
    }

    /**
     * @param array<string,mixed> $payload
     *
     * @return array{
     *   address: string,
     *   zip_code: string,
     *   city: string,
     *   state: string,
     *   country_code: string
     * }
     */
    private function resolvePayload(array $payload): array
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'address' => '',
            'zip_code' => '',
            'city' => '',
            'state' => '',
            'country_code' => '',
        ]);

        foreach (['address', 'zip_code', 'city', 'state', 'country_code'] as $key) {
            $resolver->setAllowedTypes($key, ['null', 'string']);
            $resolver->setNormalizer($key, function (Options $options, mixed $value): string {
                return $value ?? '';
            });
        }

        return $resolver->resolve($payload);
    }
}
