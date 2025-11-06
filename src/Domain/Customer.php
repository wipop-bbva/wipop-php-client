<?php

declare(strict_types=1);

namespace Wipop\Domain;

use DateTimeImmutable;
use Symfony\Component\Serializer\Attribute\SerializedName;

final class Customer
{
    public ?string $id = null;
    public ?string $name = null;
    public ?string $email = null;

    #[SerializedName('last_name')]
    public ?string $lastName = null;

    #[SerializedName('phone_number')]
    public ?string $phoneNumber = null;

    public ?Address $address = null;

    #[SerializedName('external_id')]
    public ?string $externalId = null;

    #[SerializedName('public_id')]
    public ?string $publicId = null;

    #[SerializedName('creation_date')]
    public ?DateTimeImmutable $creationDate = null;
}
