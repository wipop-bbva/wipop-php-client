<?php

declare(strict_types=1);

namespace Wipop\Domain\Input;

final class PaymentMethod
{
    public const REDIRECT = 'REDIRECT';
    public const CARD = 'CARD';
    public const BIZUM = 'BIZUM';
    public const GOOGLE_PAY = 'GOOGLEPAY';

    public function __construct(
        private readonly string $url,
        private readonly string $type = self::REDIRECT,
    ) {
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
