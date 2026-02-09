<?php

declare(strict_types=1);

namespace Wipop\Operations\Charge\Params;

use DateTimeImmutable;
use InvalidArgumentException;
use Wipop\Client\Request\RequestBuilder;
use Wipop\Domain\ChargeMethod;
use Wipop\Domain\Currency;
use Wipop\Domain\Input\Card;
use Wipop\Domain\Input\Customer;
use Wipop\Domain\Language;
use Wipop\Domain\OriginChannel;
use Wipop\Domain\PostType;
use Wipop\Domain\ProductType;
use Wipop\Domain\Value\OrderId;
use Wipop\Domain\Value\Terminal;
use Wipop\Operations\Charge\Payload\CardPayload;
use Wipop\Operations\Checkout\Payload\CustomerPayload;
use Wipop\Operations\Checkout\Payload\TerminalPayload;

use function array_key_exists;
use function sprintf;

/**
 * Charge params builder implementation.
 */
class CreateChargeParams extends RequestBuilder
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

    public function dueDate(DateTimeImmutable $dueDate): self
    {
        return $this->with('due_date', $dueDate);
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

        if (isset($parameters['due_date'])) {
            $dueDate = $parameters['due_date'];
            if (!$dueDate instanceof DateTimeImmutable) {
                throw new InvalidArgumentException('Due date must be an instance of DateTimeImmutable.');
            }

            $payload['due_date'] = $dueDate->format('Y-m-d H:i:s');
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
