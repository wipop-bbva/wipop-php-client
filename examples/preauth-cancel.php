<?php

declare(strict_types=1);

use Dotenv\Dotenv;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Wipop\Charge\ChargeMethod;
use Wipop\Charge\ChargeParams;
use Wipop\Charge\OriginChannel;
use Wipop\Charge\ReversalParams;
use Wipop\Client\ClientConfiguration;
use Wipop\Client\Environment;
use Wipop\Client\WipopClient;
use Wipop\Customer\Customer;
use Wipop\Examples\ExampleUtils;
use Wipop\Utils\Currency;
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

$logger = new Logger('wipop-preauth-cancel-example', [new StreamHandler('php://stdout')]);

$configuration = new ClientConfiguration(
    Environment::SANDBOX,
    $merchantId,
    $secretKey
);

$client = new WipopClient($configuration, $logger);

$customer = new Customer(
    'Diego',
    'Fernández',
    'diego.fernandez@example.com'
);

$preauthParams = (new ChargeParams())
    ->amount(30.0)
    ->method(ChargeMethod::CARD)
    ->currency(Currency::EUR)
    ->description('Preautorización a anular')
    ->orderId(OrderId::fromString(ExampleUtils::randomOrderId()))
    ->productType(ProductType::PAYMENT_LINK)
    ->originChannel(OriginChannel::API)
    ->redirectUrl('https://europe-west1-apuntalo-ec9c1.cloudfunctions.net/wipopWebhook')
    ->sendEmail(false)
    ->capture(false)
    ->terminal(new Terminal(1))
    ->customer($customer)
    ->language('es');

try {
    $preauthResponse = $client->chargeOperation()->create($preauthParams);
} catch (Throwable $exception) {
    $logger->error('Preauthorization creation failed', ['exception' => $exception]);
    fwrite(STDERR, sprintf("Preauthorization failed: %s\n", $exception->getMessage()));
    exit(1);
}

$transactionId = $preauthResponse->id;

if ($transactionId === null || $transactionId === '') {
    $logger->error('Transaction ID not present in preauthorization response', ['response' => $preauthResponse]);
    fwrite(STDERR, "Cannot continue without transaction ID.\n");
    exit(1);
}

$reversalParams = (new ReversalParams())
    ->reason('PRE_REVERSAL');

try {
    $reversalResponse = $client->chargeOperation()->reversal($transactionId, $reversalParams);
} catch (Throwable $exception) {
    $logger->error('Reversal of preauthorization failed', ['exception' => $exception]);
    fwrite(STDERR, sprintf("Reversal failed: %s\n", $exception->getMessage()));
    exit(1);
}

printf(
    "Preauth reversed!\nID: %s\nFInal state: %s\n",
    $reversalResponse->id ?? $transactionId,
    $reversalResponse->status->value ?? 'UNKNOWN'
);
