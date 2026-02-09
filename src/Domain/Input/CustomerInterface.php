<?php

declare(strict_types=1);

namespace Wipop\Domain\Input;

use DateTimeImmutable;

interface CustomerInterface
{
    public function getName(): string;

    public function getLastName(): string;

    public function getEmail(): string;

    public function getPhoneNumber(): ?string;

    public function getPublicId(): ?string;

    public function getExternalId(): ?string;

    public function getAddress(): ?Address;

    public function getCreationDate(): ?DateTimeImmutable;
}
