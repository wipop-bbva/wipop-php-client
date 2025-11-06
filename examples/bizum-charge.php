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

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/exampleUtils.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
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
    ->setAmount(15.00)
    ->setMethod(ChargeMethod::BIZUM)
    ->setProductType(ProductType::PAYMENT_LINK)
    ->setOriginChannel(OriginChannel::API)
    ->setTerminal(new Terminal(1))
    ->setOrderId(OrderId::fromString(ExampleUtils::randomOrderId()))
    ->setDescription('Compra test Bizum')
    ->setRedirectUrl('https://miweb.com/callback')
    ->setCurrency('EUR')
    ->setCapture(true)
    ->setCustomer($customer)
    ->setLanguage('es')
    ->setSendEmail(false);

try {
    $response = $client->chargeOperation()->create($chargeParams);
} catch (Throwable $exception) {
    $logger->error('Bizum charge example failed', ['exception' => $exception]);
    fwrite(STDERR, sprintf("Charge failed: %s\n", $exception->getMessage()));
    exit(1);
}

printf(
    "Bizum charge created!\nStatus: %s\nTransaction ID: %s\n",
    $response['status'] ?? 'UNKNOWN',
    $response['id'] ?? 'N/A'
);
