<?php

declare(strict_types=1);

namespace Wipop\Operations\Checkout\Payload;

use Wipop\Domain\Value\Terminal;

final class TerminalPayload
{
    /**
     * @return array{id:int}
     */
    public static function fromTerminal(Terminal $terminal): array
    {
        return [
            'id' => $terminal->getId(),
        ];
    }
}
