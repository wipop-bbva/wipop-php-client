<?php

declare(strict_types=1);

namespace Wipop\Tests\Charge;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Wipop\Charge\CaptureParams;

/**
 * @internal
 */
#[CoversClass(CaptureParams::class)]
final class CaptureParamsTest extends TestCase
{
    #[Test]
    public function itRequiresAmount(): void
    {
        $params = new CaptureParams();
        $this->expectException(InvalidArgumentException::class);

        $params->toArray();
    }

    #[Test]
    public function itValidatesNumericAmount(): void
    {
        $params = (new CaptureParams())->amount(25.5);
        $payload = $params->toArray();

        $this->assertSame(25.5, $payload['amount']);
    }
}
