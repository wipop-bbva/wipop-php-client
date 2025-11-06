<?php

declare(strict_types=1);

namespace Wipop\Charge;

use InvalidArgumentException;
use Wipop\CardPayment\Card;
use Wipop\Charge\Payload\CardPayload;
use Wipop\Checkout\Payload\CustomerPayload;
use Wipop\Checkout\Payload\TerminalPayload;
use Wipop\Client\Request\RequestBuilder;
use Wipop\Customer\Customer;
use Wipop\Utils\Currency;
use Wipop\Utils\Language;
use Wipop\Utils\OrderId;
use Wipop\Utils\ProductType;
use Wipop\Utils\Terminal;

use function array_key_exists;
use function sprintf;

/**
 * Charge params builder implementation.
 */
final class ChargeParams extends RequestBuilder
{
    public function amount(float $amount): self
    {
        return $this->with('amount', $amount);
    }

    public function method(string $method): self
    {
        return $this->with('method', $method);
    }

    public function description(string $description): self
    {
        return $this->with('description', $description);
    }

    public function sendEmail(bool $sendEmail): self
    {
        return $this->with('send_email', $sendEmail);
    }

    public function currency(string $currency): self
    {
        return $this->with('currency', $currency);
    }

    public function originChannel(string $originChannel): self
    {
        return $this->with('origin_channel', $originChannel);
    }

    public function productType(string $productType): self
    {
        return $this->with('product_type', $productType);
    }

    public function terminal(Terminal $terminal): self
    {
        return $this->with('terminal', $terminal);
    }

    public function redirectUrl(string $redirectUrl): self
    {
        return $this->with('redirect_url', $redirectUrl);
    }

    public function orderId(OrderId $orderId): self
    {
        return $this->with('order_id', $orderId);
    }

    public function customer(?Customer $customer): self
    {
        return $this->with('customer', $customer);
    }

    public function capture(bool $capture): self
    {
        return $this->with('capture', $capture);
    }

    public function language(string $language): self
    {
        return $this->with('language', $language);
    }

    public function card(Card $card): self
    {
        return $this->with('card', $card);
    }

    public function sourceId(string $sourceId): self
    {
        return $this->with('source_id', $sourceId);
    }

    public function useCof(bool $useCof): self
    {
        return $this->with('use_cof', $useCof);
    }

    public function postType(PostType $postType): self
    {
        return $this->with('post_type', $postType);
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
            throw new InvalidArgumentException('Charge amount is required.');
        }

        if (!isset($parameters['terminal'])) {
            throw new InvalidArgumentException('Charge terminal is required.');
        }

        $terminal = $parameters['terminal'];
        if (!$terminal instanceof Terminal) {
            throw new InvalidArgumentException('Terminal parameter must be an instance of Terminal.');
        }

        $orderId = $parameters['order_id'] ?? null;

        $payload = [
            'amount' => (float) $parameters['amount'],
            'method' => $this->normalizeMethod($parameters['method'] ?? null),
            'description' => $parameters['description'] ?? '',
            'send_email' => $parameters['send_email'] ?? false,
            'currency' => $parameters['currency'] ?? Currency::EUR,
            'origin_channel' => $parameters['origin_channel'] ?? OriginChannel::API,
            'product_type' => $parameters['product_type'] ?? ProductType::PAYMENT_GATEWAY,
            'terminal' => TerminalPayload::fromTerminal($terminal),
            'redirect_url' => $parameters['redirect_url'] ?? '',
            'order_id' => $orderId instanceof OrderId ? $orderId->value() : '',
            'language' => $parameters['language'] ?? Language::SPANISH,
        ];

        $customer = $parameters['customer'] ?? null;
        if ($customer instanceof Customer) {
            $payload['customer'] = CustomerPayload::fromCustomer($customer);
        }

        $payload['capture'] = array_key_exists('capture', $parameters)
            ? (bool) $parameters['capture']
            : true;

        if (isset($parameters['source_id'])) {
            $payload['source_id'] = (string) $parameters['source_id'];
        }

        if (array_key_exists('use_cof', $parameters)) {
            $payload['use_cof'] = (bool) $parameters['use_cof'];
        }

        if (isset($parameters['post_type'])) {
            $postType = $parameters['post_type'];
            if (!$postType instanceof PostType) {
                throw new InvalidArgumentException('Post type must be an instance of PostType.');
            }

            $payload['post_type'] = $postType->toArray();
        }

        $card = $parameters['card'] ?? null;
        if ($card instanceof Card) {
            $payload['card'] = CardPayload::fromCard($card);
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

    public function getMethod(): string
    {
        $parameters = $this->parameters();

        return $this->normalizeMethod($parameters['method'] ?? null);
    }

    private function normalizeMethod(?string $method): string
    {
        $normalized = strtoupper($method);

        if ($normalized !== ChargeMethod::CARD && $normalized !== ChargeMethod::BIZUM) {
            throw new InvalidArgumentException(sprintf('Unsupported charge method "%s".', $method));
        }

        return $normalized;
    }
}
