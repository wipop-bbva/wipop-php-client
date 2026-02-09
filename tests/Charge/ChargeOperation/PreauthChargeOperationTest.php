<?php

declare(strict_types=1);

namespace Wipop\Tests\Charge\ChargeOperation;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Wipop\Domain\Charge;
use Wipop\Domain\ChargeMethod;
use Wipop\Domain\OriginChannel;
use Wipop\Domain\ProductType;
use Wipop\Domain\TransactionStatus;
use Wipop\Domain\Value\OrderId;
use Wipop\Domain\Value\Terminal;
use Wipop\Operations\Charge\ChargeOperation;
use Wipop\Operations\Charge\Params\CaptureParams;
use Wipop\Operations\Charge\Params\ConfirmChargeParams;
use Wipop\Operations\Charge\Params\CreateChargeParams;
use Wipop\Operations\Charge\Params\ReversalParams;

/**
 * @internal
 */
#[CoversClass(ChargeOperation::class)]
final class PreauthChargeOperationTest extends AbstractChargeOperationTestCase
{
    #[Test]
    public function itCreatesAndManagesPreauthorizationFlow(): void
    {
        $history = [];
        $operation = $this->createOperationWithMockResponses([
            $this->successResponse([
                'status' => 'IN_PROGRESS',
                'payment_method' => null,
            ]),
            $this->successResponse(['status' => 'COMPLETED']),
            $this->successResponse(['status' => 'FAILED']),
        ], $history);

        $preauthParams = (new CreateChargeParams())
            ->amount(50.0)
            ->method(ChargeMethod::CARD)
            ->productType(ProductType::PAYMENT_GATEWAY)
            ->originChannel(OriginChannel::API)
            ->terminal(new Terminal(1))
            ->orderId(OrderId::fromString(self::ORDER_ID))
            ->capture(false)
            ->description('Preauthorization example')
        ;

        $createResponse = $operation->create($preauthParams);

        $captureParams = (new CaptureParams())->amount(50.0);

        $confirmResponse = $operation->capture('txn_123', $captureParams);

        $reversalParams = (new ReversalParams())->reason('CLIENT_REQUEST');

        $reversalResponse = $operation->reversal('txn_123', $reversalParams);

        $this->assertInstanceOf(Charge::class, $createResponse);
        $this->assertSame(TransactionStatus::IN_PROGRESS, $createResponse->status);
        $this->assertNull($createResponse->paymentMethod);

        $this->assertInstanceOf(Charge::class, $confirmResponse);
        $this->assertSame(TransactionStatus::COMPLETED, $confirmResponse->status);

        $this->assertInstanceOf(Charge::class, $reversalResponse);
        $this->assertSame(TransactionStatus::FAILED, $reversalResponse->status);

        $firstPayload = $this->decodeRequestBody($history[0]['request']);
        $this->assertFalse($firstPayload['capture']);
        $this->assertSame('Preauthorization example', $firstPayload['description']);

        $confirmPayload = $this->decodeRequestBody($history[1]['request']);
        $this->assertSame(50.0, (float) ($confirmPayload['amount'] ?? 0));

        $confirmPath = $history[1]['request']->getUri()->getPath();
        $this->assertSame('/c/v1/' . self::MERCHANT_ID . '/charges/txn_123/capture', $confirmPath);

        $reversalPayload = $this->decodeRequestBody($history[2]['request']);
        $this->assertSame('CLIENT_REQUEST', $reversalPayload['reason']);

        $reversalPath = $history[2]['request']->getUri()->getPath();
        $this->assertSame('/c/v1/' . self::MERCHANT_ID . '/charges/txn_123/reversal', $reversalPath);
    }

    #[Test]
    public function itUsesConfirmEndpointForChargeConfirmation(): void
    {
        $history = [];
        $operation = $this->createOperationWithMockResponses([
            $this->successResponse(['status' => 'COMPLETED']),
        ], $history);

        $confirmResponse = $operation->confirm(
            'txn_123',
            (new ConfirmChargeParams())->amount(40.0)
        );

        $this->assertInstanceOf(Charge::class, $confirmResponse);
        $this->assertSame(TransactionStatus::COMPLETED, $confirmResponse->status);

        $confirmPath = $history[0]['request']->getUri()->getPath();
        $this->assertSame('/c/v1/' . self::MERCHANT_ID . '/charges/txn_123/confirm', $confirmPath);

        $confirmPayload = $this->decodeRequestBody($history[0]['request']);
        $this->assertSame(40.0, (float) ($confirmPayload['amount'] ?? 0));
    }
}
