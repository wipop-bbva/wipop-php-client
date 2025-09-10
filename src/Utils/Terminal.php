<?php declare(strict_types=1);

namespace Wipop\Utils;

final class Terminal
{
    public function __construct(
      private readonly int $id
    ) {}

    public function getId(): int
    {
        return $this->id;
    }
}