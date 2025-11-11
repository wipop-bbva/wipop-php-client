<?php

declare(strict_types=1);

use Dotenv\Dotenv;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Wipop\Checkout\CheckoutParams;
use Wipop\Client\ClientConfiguration;
use Wipop\Client\Environment;
use Wipop\Client\WipopClient;
use Wipop\Customer\Customer;
use Wipop\Examples\ExampleUtils;
use Wipop\Utils\OrderId;
use Wipop\Utils\ProductType;
use Wipop\Utils\Terminal;

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

$configuration = new ClientConfiguration(
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

try {
    $response = $client->checkoutOperation()->create($checkout);
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
