<?php

declare(strict_types=1);

namespace Wipop\Tests\Charge\ChargeOperation;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Wipop\Domain\Charge;
use Wipop\Domain\ChargeMethod;
use Wipop\Domain\Currency;
use Wipop\Domain\OriginChannel;
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
final class BizumChargeOperationTest extends AbstractChargeOperationTestCase
{
    #[Test]
    public function itCreatesBizumChargeForExistingCustomer(): void
    {
        $history = [];
        $operation = $this->createOperationWithMockResponses([$this->successResponse()], $history);

        $params = (new CreateChargeParams())
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

        $response = $operation->createCustomerCharge('cust_123', $params);
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
