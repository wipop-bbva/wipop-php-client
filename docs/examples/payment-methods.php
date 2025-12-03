<?php

declare(strict_types=1);

use Dotenv\Dotenv;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Wipop\Client\ClientConfiguration;
use Wipop\Client\Environment;
use Wipop\Client\WipopClient;
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

$logger = new Logger('wipop-payment-methods-example', [new StreamHandler('php://stdout')]);

$configuration = new ClientConfiguration(
    Environment::SANDBOX,
    $merchantId,
    $secretKey
);

$client = new WipopClient($configuration, $logger);

try {
    $methods = $client
        ->merchantOperation()
        ->listPaymentMethods(ProductType::PASARELA_PAGO, new Terminal(1));
} catch (Throwable $exception) {
    $logger->error('Unable to fetch payment methods', ['exception' => $exception]);
    fwrite(STDERR, sprintf("Fetch failed: %s\n", $exception->getMessage()));
    exit(1);
}

if ($methods === []) {
    printf("No payment methods enabled for the current terminal.\n");
    exit(0);
}

printf("Payment methods available for terminal 1:\n");

foreach ($methods as $method) {
    printf("- %s\n", $method);
}
