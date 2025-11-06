<?php

declare(strict_types=1);

namespace Wipop\Domain;

use Symfony\Component\Serializer\Attribute\SerializedName;

final class Address
{
    public ?string $line1 = null;
    public ?string $line2 = null;
    public ?string $line3 = null;
    public ?string $city = null;
    public ?string $state = null;

    #[SerializedName('postal_code')]
    public ?string $postalCode = null;

    #[SerializedName('country_code')]
    public ?string $countryCode = null;
}
