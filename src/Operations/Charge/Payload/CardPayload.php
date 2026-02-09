<?php

declare(strict_types=1);

namespace Wipop\Operations\Charge\Payload;

use Wipop\Domain\Input\Card;

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
            'card_number' => $card->getCardNumber(),
            'holder_name' => $card->getHolderName(),
            'expiration_year' => $card->getExpirationYear(),
            'expiration_month' => $card->getExpirationMonth(),
            'cvv2' => $card->getCvv2(),
        ];
    }
}
