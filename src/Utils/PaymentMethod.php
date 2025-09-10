<?php declare(strict_types=1);

namespace Wipop\Utils;

final class PaymentMethod
{
    public const REDIRECT = 'REDIRECT';
    public const CARD = 'CARD';
    public const BIZUM = 'BIZUM';
    public const GOOGLE_PAY = 'GOOGLEPAY';

    /**
     * @param string $url
     * @param string $type
     */
    public function __construct(
      private readonly string $url,
      private readonly string $type = self::REDIRECT,
    ) {}

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
}
