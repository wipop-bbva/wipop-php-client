<?php

declare(strict_types=1);

namespace Wipop\Domain\Input;

use DateTimeImmutable;

final class NullCustomer implements CustomerInterface
{
    public function getName(): string
    {
        return '';
    }

    public function getLastName(): string
    {
        return '';
    }

    public function getEmail(): string
    {
        return '';
    }

    public function getPhoneNumber(): ?string
    {
        return null;
    }

    public function getPublicId(): ?string
    {
        return null;
    }

    public function getExternalId(): ?string
    {
        return null;
    }

    public function getAddress(): ?Address
    {
        return null;
    }

    public function getCreationDate(): ?DateTimeImmutable
    {
        return null;
    }
}
