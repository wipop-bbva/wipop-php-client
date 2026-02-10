<?php

declare(strict_types=1);

use Dotenv\Dotenv;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Wipop\Domain\ChargeMethod;
use Wipop\Operations\Charge\Params\CreateChargeParams;
use Wipop\Domain\OriginChannel;
use Wipop\Client\WipopClientConfiguration;
use Wipop\Client\Environment;
use Wipop\Client\WipopClient;
use Wipop\Domain\Input\Customer;
use Wipop\Examples\ExampleUtils;
use Wipop\Domain\Value\OrderId;
use Wipop\Domain\ProductType;
use Wipop\Domain\Value\Terminal;
use Wipop\Domain\Currency;

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

$logger = new Logger('wipop-card-charge-example', [new StreamHandler('php://stdout')]);

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
    publicId: null,
    externalId: 'ext999',
    phoneNumber: '+34611111111',
    address: null
);
$chargeParams = (new CreateChargeParams())
    ->amount(15.00)
    ->method(ChargeMethod::CARD)
    ->productType(ProductType::PAYMENT_LINK)
    ->originChannel(OriginChannel::API)
    ->terminal(new Terminal(1))
    ->orderId(OrderId::fromString(ExampleUtils::randomOrderId()))
    ->description('Compra test tarjeta')
    ->redirectUrl('https://miweb.com/callback')
    ->currency(Currency::EUR)
    ->capture(true)
    ->customer($customer)
    ->language('es')
    ->sendEmail(false);

try {
    $response = $client->chargeOperation()->create($chargeParams);
} catch (Throwable $exception) {
    $logger->error('Card charge example failed', ['exception' => $exception]);
    fwrite(STDERR, sprintf("Charge failed: %s\n", $exception->getMessage()));
    exit(1);
}

printf(
    "Card charge created successfully!\nStatus: %s\nTransaction ID: %s\n",
    $response->status->value ?? 'UNKNOWN',
    $response->id ?? 'N/A'
);
