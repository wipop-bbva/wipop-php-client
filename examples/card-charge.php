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
use Wipop\Utils\Currency;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/utils.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$merchantId = $_ENV['WIPOP_MERCHANT_ID'] ?? null;
$secretKey = $_ENV['WIPOP_SECRET_KEY'] ?? null;

if ($merchantId === false || $secretKey === false) {
    fwrite(STDERR, "No env vars detected\n");
    exit(1);
}

$logger = new Logger('wipop-card-charge-example', [new StreamHandler('php://stdout')]);

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
    publicId: null,
    externalId: 'ext999',
    phoneNumber: '+34611111111',
    address: null
);
$chargeParams = (new ChargeParams())
    ->setAmount(15.00)
    ->setMethod(ChargeMethod::CARD)
    ->setProductType(ProductType::PAYMENT_LINK)
    ->setOriginChannel(OriginChannel::API)
    ->setTerminal(new Terminal(1))
    ->setOrderId(OrderId::fromString(ExampleUtils::randomOrderId()))
    ->setDescription('Compra test tarjeta')
    ->setRedirectUrl('https://miweb.com/callback')
    ->setCurrency(Currency::EUR)
    ->setCapture(true)
    ->setCustomer($customer)
    ->setLanguage('es')
    ->setSendEmail(false);

try {
    $response = $client->chargeOperation()->create($chargeParams);
} catch (Throwable $exception) {
    $logger->error('Card charge example failed', ['exception' => $exception]);
    fwrite(STDERR, sprintf("Charge failed: %s\n", $exception->getMessage()));
    exit(1);
}

printf(
    "Charge created successfully!\nStatus: %s\nTransaction ID: %s\n",
    $response['status'] ?? 'UNKNOWN',
    $response['id'] ?? 'N/A'
);
