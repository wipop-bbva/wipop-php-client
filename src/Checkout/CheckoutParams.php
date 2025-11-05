<?php

declare(strict_types=1);

namespace Wipop\Checkout;

use DateTimeImmutable;
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

/**
 * Checkout params builder implementation.
 */
final class CheckoutParams extends RequestBuilder
{
    public function setAmount(float $amount): self
    {
        return $this->with('amount', $amount);
    }

    public function setCurrency(string $currency): self
    {
        return $this->with('currency', $currency);
    }

    public function setOrderId(OrderId $orderId): self
    {
        return $this->with('order_id', $orderId);
    }

    public function setDescription(string $description): self
    {
        return $this->with('description', $description);
    }

    public function setProductType(string $productType): self
    {
        return $this->with('product_type', $productType);
    }

    public function setOrigin(string $origin): self
    {
        return $this->with('origin', $origin);
    }

    public function setRedirectUrl(string $redirectUrl): self
    {
        return $this->with('redirect_url', $redirectUrl);
    }

    public function setSendEmail(bool $sendEmail): self
    {
        return $this->with('send_email', $sendEmail);
    }

    public function setCustomer(?Customer $customer): self
    {
        return $this->with('customer', $customer);
    }

    public function setTerminal(Terminal $terminal): self
    {
        return $this->with('terminal', $terminal);
    }

    public function setCapture(bool $capture): self
    {
        return $this->with('capture', $capture);
    }

    public function setExpirationDate(DateTimeImmutable $expirationDate): self
    {
        return $this->with('expiration_date', $expirationDate);
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
            $payload['expiration_date'] = $expirationDate->format('Y-m-d H:i:s');
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

    public static function fromCheckout(Checkout $checkout): self
    {
        $params = new self();
        $params->setAmount($checkout->getAmount())
            ->setProductType($checkout->getProductType())
            ->setTerminal($checkout->getTerminal())
            ->setCurrency($checkout->getCurrency())
            ->setOrigin($checkout->getOrigin())
            ->setSendEmail($checkout->isSendEmail())
        ;

        if ($checkout->getOrderId() !== null) {
            $params->setOrderId($checkout->getOrderId());
        }

        if ($checkout->getCustomer() !== null) {
            $params->setCustomer($checkout->getCustomer());
        }

        if ($checkout->getRedirectUrl() !== null) {
            $params->setRedirectUrl($checkout->getRedirectUrl());
        }

        if ($checkout->getDescription() !== null) {
            $params->setDescription($checkout->getDescription());
        }

        return $params;
    }
}
