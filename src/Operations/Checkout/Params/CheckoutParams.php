<?php

declare(strict_types=1);

namespace Wipop\Operations\Checkout\Params;

use DateTimeImmutable;
use InvalidArgumentException;
use Wipop\Client\Request\RequestBuilder;
use Wipop\Domain\Currency;
use Wipop\Domain\Input\Customer;
use Wipop\Domain\Language;
use Wipop\Domain\Origin;
use Wipop\Domain\ProductType;
use Wipop\Domain\Value\OrderId;
use Wipop\Domain\Value\Terminal;
use Wipop\Operations\Checkout\Payload\CustomerPayload;
use Wipop\Operations\Checkout\Payload\TerminalPayload;

/**
 * Checkout params builder implementation.
 */
final class CheckoutParams extends RequestBuilder
{
    public function amount(float $amount): self
    {
        return $this->with('amount', $amount);
    }

    public function currency(string $currency): self
    {
        return $this->with('currency', $currency);
    }

    public function orderId(OrderId $orderId): self
    {
        return $this->with('order_id', $orderId);
    }

    public function description(string $description): self
    {
        return $this->with('description', $description);
    }

    public function productType(string $productType): self
    {
        return $this->with('product_type', $productType);
    }

    public function origin(string $origin): self
    {
        return $this->with('origin', $origin);
    }

    public function redirectUrl(string $redirectUrl): self
    {
        return $this->with('redirect_url', $redirectUrl);
    }

    public function sendEmail(bool $sendEmail): self
    {
        return $this->with('send_email', $sendEmail);
    }

    public function customer(?Customer $customer): self
    {
        return $this->with('customer', $customer);
    }

    public function terminal(Terminal $terminal): self
    {
        return $this->with('terminal', $terminal);
    }

    public function capture(bool $capture): self
    {
        return $this->with('capture', $capture);
    }

    public function expirationDate(DateTimeImmutable $expirationDate): self
    {
        return $this->with('expiration_date', $expirationDate);
    }

    public function language(string $language): self
    {
        return $this->with('language', $language);
    }

    /**
     * Builds the payload as an array ready to jsonify.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $parameters = $this->parameters();

        if (!isset($parameters['amount'])) {
            throw new InvalidArgumentException('Checkout amount is required.');
        }

        if (!isset($parameters['terminal'])) {
            throw new InvalidArgumentException('Checkout terminal is required.');
        }

        $customer = $parameters['customer'] ?? null;

        $payload = [
            'amount' => (float) $parameters['amount'],
            'currency' => $parameters['currency'] ?? Currency::EUR,
            'product_type' => $parameters['product_type'] ?? ProductType::PAYMENT_GATEWAY,
            'origin' => $parameters['origin'] ?? Origin::API,
            'send_email' => $parameters['send_email'] ?? false,
            'terminal' => TerminalPayload::fromTerminal($parameters['terminal']),
            'language' => $parameters['language'] ?? Language::SPANISH,
            'customer' => CustomerPayload::fromCustomer($customer instanceof Customer ? $customer : null),
        ];

        if (isset($parameters['order_id'])) {
            /** @var OrderId $orderId */
            $orderId = $parameters['order_id'];
            $payload['order_id'] = $orderId->value();
        }

        foreach (['description', 'redirect_url'] as $optionalKey) {
            if (isset($parameters[$optionalKey])) {
                $payload[$optionalKey] = $parameters[$optionalKey];
            }
        }

        if (isset($parameters['capture'])) {
            $payload['capture'] = (bool) $parameters['capture'];
        }

        if (isset($parameters['expiration_date'])) {
            /** @var DateTimeImmutable $expirationDate */
            $expirationDate = $parameters['expiration_date'];
            $payload['expiration_date'] = $expirationDate->format('Y-m-d\TH:i:s');
        }

        return $payload;
    }

    public function getCustomer(): ?Customer
    {
        $parameters = $this->parameters();
        if (!isset($parameters['customer'])) {
            return null;
        }

        $customer = $parameters['customer'];

        return $customer instanceof Customer ? $customer : null;
    }
}
