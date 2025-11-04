<?php

declare(strict_types=1);

namespace Wipop\Checkout\Payload;

use Wipop\Checkout\Checkout;

final class CheckoutPayload
{
    /**
     * @return array<string, mixed>
     */
    public static function fromCheckout(Checkout $checkout): array
    {
        $payload = [
            'amount' => $checkout->getAmount(),
            'send_email' => $checkout->isSendEmail(),
            'currency' => $checkout->getCurrency(),
            'origin' => $checkout->getOrigin(),
            'product_type' => $checkout->getProductType(),
            'terminal' => TerminalPayload::fromTerminal($checkout->getTerminal()),
            'customer' => CustomerPayload::fromCustomer($checkout->getCustomer()),
        ];

        if ($checkout->getDescription() !== null) {
            $payload['description'] = $checkout->getDescription();
        }

        if ($checkout->getRedirectUrl() !== null) {
            $payload['redirect_url'] = $checkout->getRedirectUrl();
        }

        if ($checkout->getOrderId() !== null) {
            $payload['order_id'] = $checkout->getOrderId()->value();
        }

        return $payload;
    }
}
