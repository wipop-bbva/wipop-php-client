<?php

declare(strict_types=1);

namespace Wipop\Charge\Payload;

use Wipop\CardPayment\Card;

/**
 * Normalize card information into the structure expected by the API.
 */
final class CardPayload
{
    /**
     * @return array<string, string>
     */
    public static function fromCard(Card $card): array
    {
        return [
            'id' => $card->getId(),
            'number' => $card->getNumber(),
            'expiration_year' => $card->getExpirationYear(),
            'expiration_month' => $card->getExpirationMonth(),
        ];
    }
}
