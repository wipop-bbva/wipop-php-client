<?php

declare(strict_types=1);

use Dotenv\Dotenv;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Wipop\Charge\ChargeMethod;
use Wipop\Charge\ChargeParams;
use Wipop\Charge\OriginChannel;
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

$logger = new Logger('wipop-bizum-charge-example', [new StreamHandler('php://stdout')]);

$configuration = new ClientConfiguration(
    Environment::SANDBOX,
    $merchantId,
    $secretKey
);

$client = new WipopClient($configuration, $logger);

$customer = new Customer(
    'Carlos',
    'López',
    'carlos.lopez@example.com'
);

$chargeParams = (new ChargeParams())
    ->amount(15.00)
    ->method(ChargeMethod::BIZUM)
    ->productType(ProductType::PAYMENT_LINK)
    ->originChannel(OriginChannel::API)
    ->terminal(new Terminal(1))
    ->orderId(OrderId::fromString(ExampleUtils::randomOrderId()))
    ->description('Compra test Bizum')
    ->redirectUrl('https://miweb.com/callback')
    ->currency('EUR')
    ->capture(true)
    ->customer($customer)
    ->language('es')
    ->sendEmail(false);

try {
    $response = $client->chargeOperation()->create($chargeParams);
} catch (Throwable $exception) {
    $logger->error('Bizum charge example failed', ['exception' => $exception]);
    fwrite(STDERR, sprintf("Charge failed: %s\n", $exception->getMessage()));
    exit(1);
}

printf(
    "Bizum charge created!\nStatus: %s\nTransaction ID: %s\n",
    $response->status->value ?? 'UNKNOWN',
    $response->id ?? 'N/A'
);
