<?php

declare(strict_types=1);

namespace Wipop\Examples;

use function random_int;
use function strlen;

/**
 * Helper utilities shared across examples.
 */
final class ExampleUtils
{
    private const ALPHANUMERIC = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    public static function randomAlphaNumeric(int $length = 1): string
    {
        if ($length < 1) {
            return '';
        }

        $characters = self::ALPHANUMERIC;
        $charactersLength = strlen($characters);
        $result = '';

        for ($i = 0; $i < $length; $i++) {
            $index = random_int(0, $charactersLength - 1);
            $result .= $characters[$index];
        }

        return $result;
    }

    public static function randomOrderId(): string
    {
        $randomNumber = random_int(1000, 9999);

        return $randomNumber . self::randomAlphaNumeric(8);
    }

    public static function randomDeviceSessionId(): string
    {
        return self::randomAlphaNumeric(16);
    }
}
