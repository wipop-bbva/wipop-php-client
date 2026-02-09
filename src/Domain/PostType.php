<?php

declare(strict_types=1);

namespace Wipop\Domain;

final class PostType
{
    public function __construct(private readonly PostTypeMode $mode)
    {
    }

    public function getMode(): PostTypeMode
    {
        return $this->mode;
    }

    /**
     * @return array{mode: string}
     */
    public function toArray(): array
    {
        return ['mode' => $this->mode->value];
    }
}
