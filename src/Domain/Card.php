<?php

declare(strict_types=1);

namespace Wipop\Domain;

use Symfony\Component\Serializer\Attribute\SerializedName;

final class Card
{
    public ?string $id = null;
    public ?string $number = null;

    #[SerializedName('holder_name')]
    public ?string $holderName = null;

    #[SerializedName('expiration_month')]
    public ?string $expirationMonth = null;

    #[SerializedName('expiration_year')]
    public ?string $expirationYear = null;

    public ?string $cvv2 = null;

    #[SerializedName('card_number')]
    public ?string $cardNumber = null;

    public ?string $brand = null;
    public ?string $type = null;

    #[SerializedName('bank_name')]
    public ?string $bankName = null;

    #[SerializedName('bank_code')]
    public ?string $bankCode = null;

    public ?Address $address = null;
}
