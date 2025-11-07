<?php

declare(strict_types=1);

namespace Wipop\Tests\Charge\ChargeOperation;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Wipop\Charge\ChargeMethod;
use Wipop\Charge\ChargeOperation;
use Wipop\Charge\ChargeParams;
use Wipop\Charge\OriginChannel;
use Wipop\Domain\Charge;
use Wipop\Domain\TransactionStatus;
use Wipop\Utils\Currency;
use Wipop\Utils\OrderId;
use Wipop\Utils\ProductType;
use Wipop\Utils\Terminal;

/**
 * @internal
 */
#[CoversClass(ChargeOperation::class)]
final class BizumChargeOperationTest extends AbstractChargeOperationTestCase
{
    #[Test]
    public function itCreatesBizumChargeForExistingCustomer(): void
    {
        $history = [];
        $operation = $this->createOperationWithMockResponses([$this->successResponse()], $history);

        $params = (new ChargeParams())
            ->amount(45.0)
            ->method(ChargeMethod::BIZUM)
            ->terminal(new Terminal(1))
            ->productType(ProductType::PAYMENT_LINK)
            ->currency(Currency::EUR)
            ->description('Compra test Bizum')
            ->originChannel(OriginChannel::API)
            ->redirectUrl('https://miweb.com/callback')
            ->language('es')
            ->sendEmail(false)
            ->orderId(OrderId::fromString(self::ORDER_ID))
        ;

        $response = $operation->create($params, 'cust_123');
        $this->assertInstanceOf(Charge::class, $response);
        $this->assertSame(TransactionStatus::CHARGE_PENDING, $response->status);

        $this->assertRequest(
            $history[0]['request'],
            '/b/v1/' . self::MERCHANT_ID . '/customers/cust_123/charges',
            [
                'amount' => 45.0,
                'method' => ChargeMethod::BIZUM,
                'currency' => Currency::EUR,
                'product_type' => ProductType::PAYMENT_LINK,
                'origin_channel' => OriginChannel::API,
                'description' => 'Compra test Bizum',
                'redirect_url' => 'https://miweb.com/callback',
                'language' => 'es',
                'send_email' => false,
                'capture' => true,
                'terminal' => ['id' => 1],
            ]
        );

        $payload = $this->decodeRequestBody($history[0]['request']);
        $this->assertArrayNotHasKey('customer', $payload);
    }
}
