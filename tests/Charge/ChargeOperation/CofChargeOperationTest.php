<?php

declare(strict_types=1);

namespace Wipop\Tests\Charge\ChargeOperation;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Wipop\Charge\ChargeMethod;
use Wipop\Charge\ChargeOperation;
use Wipop\Charge\ChargeParams;
use Wipop\Charge\OriginChannel;
use Wipop\Charge\PostType;
use Wipop\Charge\PostTypeMode;
use Wipop\Utils\ProductType;
use Wipop\Utils\Terminal;

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
        $operation = $this->createOperationWithMockResponses([
            $this->successResponse(),
            $this->successResponse(),
        ], $history);

        $tokenizationParams = (new ChargeParams())
            ->setAmount(0.0)
            ->setMethod(ChargeMethod::CARD)
            ->setProductType(ProductType::PAYMENT_LINK)
            ->setOriginChannel(OriginChannel::API)
            ->setTerminal(new Terminal(1))
            ->setUseCof(true)
            ->setDescription('Tokenize card via hosted form')
        ;

        $operation->create($tokenizationParams);

        $token = 'card_tok_6789';

        $gatewayParams = (new ChargeParams())
            ->setAmount(35.0)
            ->setMethod(ChargeMethod::CARD)
            ->setProductType(ProductType::PAYMENT_GATEWAY)
            ->setOriginChannel(OriginChannel::API)
            ->setTerminal(new Terminal(1))
            ->setSourceId($token)
            ->setUseCof(true)
            ->setPostType(new PostType(PostTypeMode::RECURRENT))
            ->setCapture(false)
            ->setDescription('MIT recurring charge')
        ;

        $operation->create($gatewayParams);

        /** @var array<int, array{request: RequestInterface, response: null|ResponseInterface, error: mixed, options: array<string, mixed>}> $history */
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
