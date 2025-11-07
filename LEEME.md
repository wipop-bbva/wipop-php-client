# Cliente PHP de Wipop

Biblioteca en PHP para integrar la pasarela de pagos Wipop con soporte completo para cargos, checkouts y manejo de errores.

## Funcionalidades

- **Operaciones de cargo con tarjeta**
  - Generación de links de pago
  - Devoluciones
  - Creación de preautorizaciones
  - Confirmación de preautorizaciones
  - Anulación de preautorizaciones
  - Tokenización
  - Cargos one-click
  - Cargos recurrentes
- **Operaciones Bizum**
  - Creación de links de pago
  - Devoluciones
- **Operaciones de Checkout**
  - Generación de links de pago
  - Botón de pago

## Requisitos

- PHP 8.1 o superior
- Composer 2.x

## Instalación

// TODO: subir a repo composer 

## Antes de empezar
- Haber completado la identificación en el proceso de contratación de la pasarela de pago Wipöp.
- Acceder al panel de control en modo pruebas (sandbox) desde tu cuenta.
- Definir claramente qué método usarás para realizar la integración en tu sistema:

### Credenciales necesarias:
- Merchant ID
- API Key secreta
- Terminal ID (por defecto, 1 en sandbox)

## Inicio rápido

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
    'tu-merchant-id',
    'tu-secret-key'
);

$client = new WipopClient($configuration);

$params = (new ChargeParams())
    ->method(ChargeMethod::CARD)
    ->amount(100.00)
    ->currency('EUR')
    ->description('Pago del pedido #123')
    ->productType(ProductType::PAYMENT_LINK)
    ->orderId(OrderId::fromString('1234ABCDEFGH'))
    ->terminal(new Terminal(1));

$charge = $client->chargeOperation()->create($params);
```

## Configuración

### Configuración básica

```php
use Wipop\Client\ClientConfiguration;
use Wipop\Client\Environment;

$config = new ClientConfiguration(
    Environment::SANDBOX, // Environment::PRODUCTION para producción
    'merchant-id',
    'secret-key'
);
```

### Configuración HTTP

```php
use Wipop\Client\ClientConfiguration;
use Wipop\Client\ClientHttpConfiguration;
use Wipop\Client\Environment;

$httpConfig = new ClientHttpConfiguration(10_000, 45_000);

$config = new ClientConfiguration(
    Environment::SANDBOX,
    'merchant-id',
    'secret-key',
    $httpConfig
);
```

- `connectionRequestTimeout`: tiempo (ms) que se espera por un slot de conexión (por defecto 5000).
- `responseTimeout`: tiempo (ms) que se espera por la respuesta completa (por defecto 30000).

## Operaciones de cargo

### Cargo con tarjeta

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
    ->description('Pago con tarjeta')
    ->orderId(OrderId::fromString('1234ABCDEFGH'))
    ->productType(ProductType::PAYMENT_LINK)
    ->terminal(new Terminal(1));

$response = $client->chargeOperation()->create($cardCharge);
```

### Cargo Bizum

```php
$bizumCharge = (new ChargeParams())
    ->method(ChargeMethod::BIZUM)
    ->amount(50.00)
    ->currency('EUR')
    ->description('Pago Bizum')
    ->orderId(OrderId::fromString('2345ABCDEFGH'))
    ->productType(ProductType::PAYMENT_LINK)
    ->terminal(new Terminal(1));

$response = $client->chargeOperation()->create($bizumCharge, 'customer-id');
```

### Confirmar / devolver / anular / capturar

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

### Tokenización, one-click, recurrentes, preautorizaciones

Aplica `useCof(true)` para tokenizar, `sourceId()` para cargos one-click, `postType(PostTypeMode::RECURRENT)` para recurrentes y `capture(false)` para preautorizar.

## Operaciones de checkout

```php
use Wipop\Checkout\CheckoutParams;

$checkoutParams = (new CheckoutParams())
    ->amount(49.90)
    ->currency('EUR')
    ->description('Checkout de ejemplo')
    ->orderId(OrderId::fromString('3456ABCDEFGH'))
    ->productType(ProductType::PAYMENT_GATEWAY)
    ->terminal(new Terminal(1))
    ->redirectUrl('https://tu-sitio.com/success')
    ->sendEmail(true);

$checkout = $client->checkoutOperation()->create($checkoutParams);
```

### Checkout para un cliente existente

Si el cliente ya existe en Wipop, envía una instancia de `Wipop\Customer\Customer` con su `publicId`.
La librería llamará automáticamente a `/customers/{customerId}/checkouts`.

```php
use Wipop\Customer\Customer;

$clienteExistente = new Customer(
    name: 'Ana',
    lastName: 'García',
    email: 'ana@example.com',
    publicId: 'cust_1234567890'
);

$checkoutParams = (new CheckoutParams())
    ->amount(120.00)
    ->currency('EUR')
    ->description('Checkout para cliente existente')
    ->orderId(OrderId::fromString('9012ABCDEFGH'))
    ->productType(ProductType::PAYMENT_GATEWAY)
    ->terminal(new Terminal(1))
    ->customer($clienteExistente);

$checkout = $client->checkoutOperation()->create($checkoutParams);
```

## Manejo de errores

```php
use Wipop\Client\Exception\WipopApiException;

try {
    $charge = $client->chargeOperation()->create($params);
} catch (WipopApiException $exception) {
    // Maneja el error según código
}
```

## Referencia de API

- **WipopClient**: punto de entrada.
- **ClientConfiguration / ClientHttpConfiguration / Environment**: configuración del cliente.
- **ChargeOperation**: creación, confirmación, devolución, anulación y captura.
- **CheckoutOperation**: gestión de sesiones de checkout.
- **ChargeParams / CaptureParams / RefundParams / ReversalParams**.
- **CheckoutParams**.
- **Modelos de dominio**: `Charge`, `Checkout`, `Customer`, `Terminal`, `PaymentMethod`, `TransactionStatus`.


## Soporte

Para soporte y preguntas, por favor contacta al equipo de soporta o [crea un issue](https://docs.github.com/en/issues/tracking-your-work-with-issues/learning-about-issues/quickstart) en el repositorio del proyecto.
