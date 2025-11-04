<?php

declare(strict_types=1);

namespace Wipop\Tests\Charge\ChargeOperation;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Wipop\Charge\ChargeMethod;
use Wipop\Charge\ChargeOperation;
use Wipop\Charge\ChargeParams;
use Wipop\Charge\OriginChannel;
use Wipop\Customer\Customer;
use Wipop\Utils\Currency;
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

        $customer = new Customer(
            name: 'Carlos',
            lastName: 'López',
            email: 'carlos.lopez@example.com',
            publicId: 'cust_123'
        );

        $params = (new ChargeParams())
            ->setAmount(45.0)
            ->setMethod(ChargeMethod::BIZUM)
            ->setTerminal(new Terminal(1))
            ->setProductType(ProductType::PAYMENT_LINK)
            ->setCurrency(Currency::EUR)
            ->setDescription('Compra test Bizum')
            ->setOriginChannel(OriginChannel::API)
            ->setRedirectUrl('https://miweb.com/callback')
            ->setLanguage('es')
            ->setSendEmail(false)
            ->setCustomer($customer)
        ;

        $operation->create($params);

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
    }
}
