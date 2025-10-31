<?php

declare(strict_types=1);

use Dotenv\Dotenv;
use Wipop\Checkout\CheckoutParams;
use Wipop\Client\ClientConfiguration;
use Wipop\Client\Environment;
use Wipop\Client\WipopClient;
use Wipop\Customer\Customer;
use Wipop\Utils\OrderId;
use Wipop\Utils\ProductType;
use Wipop\Utils\Terminal;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
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
    publicId: null,
    externalId: 'ext999',
    phoneNumber: '+34611111111',
    address: null
);

$checkout = (new CheckoutParams())
    ->setAmount(49.95)
    ->setProductType(ProductType::PAYMENT_LINK)
    ->setTerminal(new Terminal(0))
    ->setOrderId(OrderId::fromString('8887c5RfrfKD'))
    ->setCustomer($customer)
    ->setRedirectUrl('https://miweb.com/callback')
    ->setDescription('Prueba integrador externo QA')
    ->setSendEmail(true);

try {
    $response = $client->checkoutPayment($checkout);
} catch (Throwable $exception) {
    $logger->error('Checkout example failed', ['exception' => $exception]);
    fwrite(STDERR, sprintf("Checkout failed: %s\n", $exception->getMessage()));
    exit(1);
}

printf(
    "Checkout created successfully!\nID: %s\nStatus: %s\nCheckout link: %s\n",
    $response->getId(),
    $response->getStatus(),
    $response->getCheckoutLink()
);
