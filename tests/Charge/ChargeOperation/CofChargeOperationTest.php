<?php

declare(strict_types=1);

namespace Wipop\Tests\Charge\ChargeOperation;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Wipop\Domain\Charge;
use Wipop\Domain\ChargeMethod;
use Wipop\Domain\OriginChannel;
use Wipop\Domain\PostType;
use Wipop\Domain\PostTypeMode;
use Wipop\Domain\ProductType;
use Wipop\Domain\TransactionStatus;
use Wipop\Domain\Value\OrderId;
use Wipop\Domain\Value\Terminal;
use Wipop\Operations\Charge\ChargeOperation;
use Wipop\Operations\Charge\Params\CreateChargeParams;

/**
 * @internal
 */
#[CoversClass(ChargeOperation::class)]
final class CofChargeOperationTest extends AbstractChargeOperationTestCase
{
    #[Test]
    public function itTokenizesCardAndUsesTokenForGatewayCharge(): void
    {
        $history = [];
        $token = 'card_token_6789';
        $operation = $this->createOperationWithMockResponses([
            $this->successResponse([
                'use_cof' => false,
                'card' => ['id' => $token],
            ]),
            $this->successResponse([
                'status' => 'COMPLETED',
                'use_cof' => true,
                'card' => ['id' => $token],
            ]),
        ], $history);

        $tokenizationParams = (new CreateChargeParams())
            ->amount(0.0)
            ->method(ChargeMethod::CARD)
            ->productType(ProductType::PAYMENT_LINK)
            ->originChannel(OriginChannel::API)
            ->terminal(new Terminal(1))
            ->orderId(OrderId::fromString(self::ORDER_ID))
            ->useCof(true)
            ->description('Tokenize card via wipop form')
        ;

        $response = $operation->create($tokenizationParams);
        $this->assertInstanceOf(Charge::class, $response);
        $this->assertSame(TransactionStatus::CHARGE_PENDING, $response->status);
        $this->assertFalse($response->useCof);
        $this->assertSame('card_token_6789', $response->card?->id);

        $gatewayParams = (new CreateChargeParams())
            ->amount(35.0)
            ->method(ChargeMethod::CARD)
            ->productType(ProductType::PAYMENT_GATEWAY)
            ->originChannel(OriginChannel::API)
            ->terminal(new Terminal(1))
            ->orderId(OrderId::fromString(self::ORDER_ID))
            ->sourceId($token)
            ->useCof(true)
            ->postType(new PostType(PostTypeMode::RECURRENT))
            ->capture(false)
            ->description('MIT recurring charge')
        ;

        $secondResponse = $operation->create($gatewayParams);
        $this->assertInstanceOf(Charge::class, $secondResponse);
        $this->assertSame(TransactionStatus::COMPLETED, $secondResponse->status);
        $this->assertSame($token, $secondResponse->card?->id);

        $firstPayload = $this->decodeRequestBody($history[0]['request']);
        $this->assertSame(0.0, (float) $firstPayload['amount']);
        $this->assertTrue($firstPayload['use_cof']);
        $this->assertTrue($firstPayload['capture']);
        $this->assertSame(ProductType::PAYMENT_LINK, $firstPayload['product_type']);

        $secondPayload = $this->decodeRequestBody($history[1]['request']);
        $this->assertSame($token, $secondPayload['source_id']);
        $this->assertTrue($secondPayload['use_cof']);
        $this->assertSame(['mode' => PostTypeMode::RECURRENT->value], $secondPayload['post_type']);
        $this->assertFalse($secondPayload['capture']);
    }
}
