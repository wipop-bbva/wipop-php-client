<?php

declare(strict_types=1);

use Dotenv\Dotenv;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Wipop\Operations\Checkout\Params\CheckoutParams;
use Wipop\Client\WipopClientConfiguration;
use Wipop\Client\Environment;
use Wipop\Client\WipopClient;
use Wipop\Domain\Input\Customer;
use Wipop\Examples\ExampleUtils;
use Wipop\Domain\Value\OrderId;
use Wipop\Domain\ProductType;
use Wipop\Domain\Value\Terminal;

require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/exampleUtils.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();

$merchantId = $_ENV['WIPOP_MERCHANT_ID'] ?? null;
$secretKey = $_ENV['WIPOP_SECRET_KEY'] ?? null;

if ($merchantId === false || $secretKey === false) {
    fwrite(STDERR, "No env vars detected\n");
    exit(1);
}

$logger = new Logger('wipop-checkout-example', [new StreamHandler('php://stdout')]);

$configuration = new WipopClientConfiguration(
    Environment::SANDBOX,
    $merchantId,
    $secretKey
);

$client = new WipopClient($configuration, $logger);

$customer = new Customer(
    'Ana',
    'García',
    'ana.garcia@example.com',
    publicId: 'cust_1234567890',
    externalId: 'ext999',
    phoneNumber: '+34611111111',
    address: null
);

$checkout = (new CheckoutParams())
    ->amount(49.95)
    ->productType(ProductType::PAYMENT_LINK)
    ->terminal(new Terminal(1))
    ->orderId(OrderId::fromString(ExampleUtils::randomOrderId()))
    ->customer($customer)
    ->redirectUrl('https://miweb.com/callback')
    ->description('Prueba integrador externo QA')
    ->sendEmail(true);

$customerId = $customer->getPublicId();
if ($customerId === null || $customerId === '') {
    fwrite(STDERR, "Customer public_id is required for Customer Checkout().\n");
    exit(1);
}

try {
    $response = $client->checkoutOperation()->createCustomerCheckout($customerId, $checkout);
} catch (Throwable $exception) {
    $logger->error('Checkout example failed', ['exception' => $exception]);
    fwrite(STDERR, sprintf("Checkout failed: %s\n", $exception->getMessage()));
    exit(1);
}

printf(
    "Checkout created successfully!\nID: %s\nStatus: %s\nCheckout link: %s\n",
    $response->id ?? 'N/A',
    $response->status ?? 'UNKNOWN',
    $response->checkoutLink ?? 'N/A'
);
