# Wipop PHP Client

A modern PHP client library for the Wipop payment processing API with full type safety and comprehensive error handling.

## Features

- **Card Charge Operations**
  - Payment link generation
  - Refunds
  - Pre-authorization creation
  - Pre-authorization confirmation
  - Pre-authorization reversal
  - Token generation
  - One-click charges
  - Recurring charges
- **Bizum Charge Operations**
  - Payment link creation
  - Refunds
- **Checkout Operations**
  - Payment link generation
  - Payment button
- **Merchant Operations**
  - Payment method discovery per terminal

## Requirements

- PHP 8.1 or higher
- Composer 2.x

## Installation

// TODO

## Before Getting Started
- Have completed the identification process during the Wipöp payment gateway contracting.
- Access the control panel in test mode (sandbox) from your account.
- Clearly define which method you will use to perform the integration in your system:

## Quick Start

```php
use Wipop\Charge\ChargeMethod;
use Wipop\Charge\ChargeParams;
use Wipop\Client\ClientConfiguration;
use Wipop\Client\Environment;
use Wipop\Client\WipopClient;
use Wipop\Utils\OrderId;
use Wipop\Utils\ProductType;
use Wipop\Utils\Terminal;

$configuration = new ClientConfiguration(
    Environment::SANDBOX,
    'your-merchant-id',
    'your-secret-key'
);

$client = new WipopClient($configuration);

$params = (new ChargeParams())
    ->method(ChargeMethod::CARD)
    ->amount(100.00)
    ->currency('EUR')
    ->description('Payment for order #123')
    ->productType(ProductType::PAYMENT_LINK)
    ->orderId(OrderId::fromString('1234ABCDEFGH'))
    ->terminal(new Terminal(1));

$charge = $client->chargeOperation()->create($params);
```

## Configuration

### Basic Configuration

```php
use Wipop\Client\ClientConfiguration;
use Wipop\Client\Environment;

$config = new ClientConfiguration(
    Environment::SANDBOX, // Environment::PRODUCTION for live operations
    'your-merchant-id',
    'your-secret-key'
);
```

### HTTP Configuration

```php
use Wipop\Client\ClientConfiguration;
use Wipop\Client\ClientHttpConfiguration;
use Wipop\Client\Environment;

$httpConfig = new ClientHttpConfiguration(
    10_000,
    45_000
);

$config = new ClientConfiguration(
    Environment::SANDBOX,
    'merchant-id',
    'secret-key',
    $httpConfig
);
```

- `connectionRequestTimeout`: time (ms) to wait for a connection slot (default 5000).
- `responseTimeout`: time (ms) to wait for a full response (default 30000).

## Charge Operations

### Create Card Charge

```php
use Wipop\Charge\ChargeMethod;
use Wipop\Charge\ChargeParams;
use Wipop\Utils\OrderId;
use Wipop\Utils\ProductType;
use Wipop\Utils\Terminal;

$cardCharge = (new ChargeParams())
    ->method(ChargeMethod::CARD)
    ->amount(100.00)
    ->currency('EUR')
    ->description('Card payment')
    ->orderId(OrderId::fromString('1234ABCDEFGH'))
    ->productType(ProductType::PAYMENT_LINK)
    ->terminal(new Terminal(1));

$response = $client->chargeOperation()->create($cardCharge);
```

### Create Bizum Charge

```php
$bizumCharge = (new ChargeParams())
    ->method(ChargeMethod::BIZUM)
    ->amount(50.00)
    ->currency('EUR')
    ->description('Bizum payment')
    ->orderId(OrderId::fromString('2345ABCDEFGH'))
    ->productType(ProductType::PAYMENT_LINK)
    ->terminal(new Terminal(1));

$response = $client->chargeOperation()->create($bizumCharge, 'customer-id');
```

### Confirm / Refund / Reverse / Capture

```php
use Wipop\Charge\CaptureParams;
use Wipop\Charge\RefundParams;
use Wipop\Charge\ReversalParams;

$capture = (new CaptureParams())->amount(75.00);
$client->chargeOperation()->capture('transaction-id', $capture);

$refund = (new RefundParams())->amount(25.00);
$client->chargeOperation()->refund('transaction-id', $refund);

$reversal = (new ReversalParams())->reason('PRE_REVERSAL');
$client->chargeOperation()->reversal('transaction-id', $reversal);
```

### Tokenization / One-Click / Recurring / Pre-authorization

Use `ChargeParams::useCof(true)` to tokenize, `sourceId()` for one-click charges, `postType()` with `PostTypeMode::RECURRENT` for recurring payments, and `capture(false)` for pre-authorizations.

### Recurring Payments

Recurring billing is achieved with the regular `chargeOperation()->create()` flow. Configure the
payload to flag the transaction as recurring and, after the first successful charge, reuse the
generated source identifier.

```php
use Wipop\Charge\ChargeMethod;
use Wipop\Charge\ChargeParams;
use Wipop\Charge\PostType;
use Wipop\Charge\PostTypeMode;
use Wipop\Utils\Terminal;

// First charge: tokenize cardholder for future cycles
$initialCharge = (new ChargeParams())
    ->method(ChargeMethod::CARD)
    ->amount(29.99)
    ->useCof(true)
    ->postType(new PostType(PostTypeMode::RECURRENT))
    ->capture(true)
    ->redirectUrl('https://myweb.test/return')
    ->terminal(new Terminal(1));

$initialResponse = $client->chargeOperation()->create($initialCharge);
$sourceId = $initialResponse->card?->id; // persist for later cycles

// Subsequent cycles: reuse the stored sourceId
$recurringCharge = (new ChargeParams())
    ->method(ChargeMethod::CARD)
    ->amount(29.99)
    ->sourceId($sourceId)
    ->postType(new PostType(PostTypeMode::RECURRENT))
    ->terminal(new Terminal(1));

$client->chargeOperation()->create($recurringCharge);
```

## Checkout Operations

```php
use Wipop\Checkout\CheckoutParams;

$checkoutParams = (new CheckoutParams())
    ->amount(49.90)
    ->currency('EUR')
    ->description('Checkout example')
    ->orderId(OrderId::fromString('3456ABCDEFGH'))
    ->productType(ProductType::PAYMENT_GATEWAY)
    ->terminal(new Terminal(1))
    ->redirectUrl('https://your-site.com/success')
    ->sendEmail(true);

$checkout = $client->checkoutOperation()->create($checkoutParams);
```

### Checkout for an Existing Customer

If you need to create a checkout for a known customer, pass an instance of `Wipop\Customer\Customer`
containing the `publicId`. The SDK will automatically call the `/customers/{customerId}/checkouts`
endpoint.

```php
use Wipop\Customer\Customer;

$existingCustomer = new Customer(
    name: 'Ana',
    lastName: 'García',
    email: 'ana@example.com',
    publicId: 'cust_1234567890'
);

$checkoutParams = (new CheckoutParams())
    ->amount(120.00)
    ->currency('EUR')
    ->description('Customer checkout')
    ->orderId(OrderId::fromString('9012ABCDEFGH'))
    ->productType(ProductType::PAYMENT_GATEWAY)
    ->terminal(new Terminal(1))
    ->customer($existingCustomer);

$checkout = $client->checkoutOperation()->create($checkoutParams);
```

## Merchant Operations

Retrieve the payment methods enabled for a merchant/terminal combination. 

```php
use Wipop\Utils\ProductType;
use Wipop\Utils\Terminal;

$methods = $client
    ->merchantOperation()
    ->listPaymentMethods(ProductType::PAYMENT_GATEWAY, new Terminal(1));

// ['CARD', 'BIZUM', 'GOOGLE_PAY']
```

## Error Handling

```php
use Wipop\Client\Exception\WipopApiException;

try {
    $charge = $client->chargeOperation()->create($params);
} catch (WipopApiException $exception) {
    // Inspect $exception->getCode()
}
```

## API Reference

- **WipopClient**: entrypoint for API operations.
- **ClientConfiguration / ClientHttpConfiguration / Environment**: client setup.
- **ChargeOperation**: create, confirm, refund, reverse, capture charges.
- **CheckoutOperation**: manage checkout sessions.
- **MerchantOperation**: inspect merchant metadata such as enabled payment methods.
- **ChargeParams / CaptureParams / RefundParams / ReversalParams**: payload builders.
- **CheckoutParams**: checkout payload builder.
- **Domain models**: `Charge`, `Checkout`, `Customer`, `Terminal`, `PaymentMethod`, `TransactionStatus`.


## Support

For support and questions, please contact the support team or [create an issue](https://docs.github.com/en/issues/tracking-your-work-with-issues/learning-about-issues/quickstart) in the project repository.
