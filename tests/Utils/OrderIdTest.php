<?php declare(strict_types=1);

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Wipop\Utils\OrderId;

#[CoversClass(OrderId::class)]
class OrderIdTest extends TestCase
{
    private const VALID_ORDER_ID = '3277b3vzgiio';
    private const INVALID_ORDER_ID = 'invalid_order_id';

    #[Test]
    public function itShouldCreateAnOrderId(): void
    {
        $orderId = new OrderId(self::VALID_ORDER_ID);
        $this->assertInstanceOf(OrderId::class, $orderId);
        $this->assertEquals(self::VALID_ORDER_ID, $orderId->getOrderId());
    }

    #[Test]
    public function itShouldThrowAnExceptionIfOrderIdIsNotValid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new OrderId(self::INVALID_ORDER_ID);
    }
}
