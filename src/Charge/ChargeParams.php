<?php

declare(strict_types=1);

namespace Wipop\Charge;

use InvalidArgumentException;
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
 * Charge params builder implementation restricted to the supported payload fields.
 */
final class ChargeParams extends RequestBuilder
{
    public function setAmount(float $amount): self
    {
        return $this->with('amount', $amount);
    }

    public function setMethod(string $method): self
    {
        return $this->with('method', $method);
    }

    public function setDescription(string $description): self
    {
        return $this->with('description', $description);
    }

    public function setSendEmail(bool $sendEmail): self
    {
        return $this->with('send_email', $sendEmail);
    }

    public function setCurrency(string $currency): self
    {
        return $this->with('currency', $currency);
    }

    public function setOriginChannel(string $originChannel): self
    {
        return $this->with('origin_channel', $originChannel);
    }

    public function setProductType(string $productType): self
    {
        return $this->with('product_type', $productType);
    }

    public function setTerminal(Terminal $terminal): self
    {
        return $this->with('terminal', $terminal);
    }

    public function setRedirectUrl(string $redirectUrl): self
    {
        return $this->with('redirect_url', $redirectUrl);
    }

    public function setOrderId(OrderId $orderId): self
    {
        return $this->with('order_id', $orderId);
    }

    public function setCustomer(?Customer $customer): self
    {
        return $this->with('customer', $customer);
    }

    public function setCapture(bool $capture): self
    {
        return $this->with('capture', $capture);
    }

    public function setLanguage(string $language): self
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
            throw new InvalidArgumentException('Charge amount is required.');
        }

        if (!isset($parameters['terminal'])) {
            throw new InvalidArgumentException('Charge terminal is required.');
        }

        $terminal = $parameters['terminal'];
        if (!$terminal instanceof Terminal) {
            throw new InvalidArgumentException('Terminal parameter must be an instance of Terminal.');
        }

        $customer = $parameters['customer'] ?? null;

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
            'customer' => CustomerPayload::fromCustomer($customer instanceof Customer ? $customer : null),
            'language' => $parameters['language'] ?? Language::SPANISH,
        ];

        $payload['capture'] = array_key_exists('capture', $parameters)
            ? (bool) $parameters['capture']
            : true;

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
