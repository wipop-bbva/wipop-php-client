<?php

declare(strict_types=1);

namespace Wipop\Tests\Utils;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Wipop\Domain\Value\Terminal;

/**
 * @internal
 */
#[CoversClass(Terminal::class)]
final class TerminalTest extends TestCase
{
    #[Test]
    #[DataProvider('terminalIdsProvider')]
    public function itShouldExposeTheConfiguredIdentifier(int $terminalId): void
    {
        $terminal = new Terminal($terminalId);

        $this->assertSame($terminalId, $terminal->getId());
    }

    /**
     * @return iterable<string, array{0: int}>
     */
    public static function terminalIdsProvider(): iterable
    {
        yield 'zero' => [0];
        yield 'oneDigit' => [1];
        yield 'largeNumber' => [9999];
    }
}
