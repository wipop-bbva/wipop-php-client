<?php

declare(strict_types=1);

namespace Wipop\Checkout\Response;

use DateTimeImmutable;
use JsonException;
use Symfony\Component\OptionsResolver\Exception\ExceptionInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Wipop\Checkout\CheckoutResponse;
use Wipop\Utils\OrderId;

final class CheckoutResponseFactory
{
    private CustomerFactory $customerFactory;

    public function __construct(?CustomerFactory $customerFactory = null)
    {
        $this->customerFactory = $customerFactory ?? new CustomerFactory();
    }

    /**
     * @param array<string,mixed> $payload
     *
     * @throws JsonException
     */
    public function fromArray(array $payload): CheckoutResponse
    {
        try {
            $data = $this->resolvePayload($payload);
        } catch (ExceptionInterface $exception) {
            throw new JsonException($exception->getMessage(), 0, $exception);
        }

        /** @var null|array<string, mixed> $customerPayload */
        $customerPayload = $data['customer'];

        return new CheckoutResponse(
            $data['id'],
            $data['amount'],
            $data['description'],
            OrderId::fromString($data['order_id']),
            $data['status'],
            $data['checkout_link'],
            new DateTimeImmutable($data['creation_date']),
            new DateTimeImmutable($data['expiration_date']),
            $this->customerFactory->fromArray($customerPayload)
        );
    }

    /**
     * @param array<string,mixed> $payload
     *
     * @return array{
     *   id: string,
     *   amount: float,
     *   status: string,
     *   checkout_link: string,
     *   creation_date: string,
     *   expiration_date: string,
     *   order_id: string,
     *   description: string,
     *   customer: null|array<string,mixed>
     * }
     */
    private function resolvePayload(array $payload): array
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired([
            'id',
            'amount',
            'status',
            'checkout_link',
            'creation_date',
            'expiration_date',
            'order_id',
        ]);

        $resolver->setDefaults([
            'description' => '',
            'customer' => null,
        ]);

        foreach (['id', 'status', 'checkout_link', 'creation_date', 'expiration_date', 'order_id'] as $key) {
            $resolver->setAllowedTypes($key, 'string');
        }

        $resolver->setAllowedTypes('description', ['null', 'string']);
        $resolver->setNormalizer('description', function (Options $options, mixed $value): string {
            return $value ?? '';
        });

        $resolver->setAllowedTypes('customer', ['null', 'array']);
        $resolver->setAllowedTypes('amount', ['int', 'float', 'string']);
        $resolver->setAllowedValues('amount', function (mixed $value): bool {
            return is_numeric($value);
        });

        $resolver->setNormalizer('amount', function (Options $options, mixed $value): float {
            return (float) $value;
        });

        $resolved = $resolver->resolve($payload);

        return [
            'id' => $resolved['id'],
            'amount' => $resolved['amount'],
            'status' => $resolved['status'],
            'checkout_link' => $resolved['checkout_link'],
            'creation_date' => $resolved['creation_date'],
            'expiration_date' => $resolved['expiration_date'],
            'order_id' => $resolved['order_id'],
            'description' => $resolved['description'],
            'customer' => $resolved['customer'],
        ];
    }
}
