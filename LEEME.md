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
- **Operaciones de merchant**
  - Consulta de métodos de pago por terminal

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
use Wipop\Domain\ChargeMethod;
use Wipop\Operations\Charge\Params\CreateChargeParams;
use Wipop\Client\WipopClientConfiguration;
use Wipop\Client\Environment;
use Wipop\Client\WipopClient;
use Wipop\Domain\Value\OrderId;
use Wipop\Domain\ProductType;
use Wipop\Domain\Value\Terminal;

$configuration = new WipopClientConfiguration(
    Environment::SANDBOX,
    'tu-merchant-id',
    'tu-secret-key'
);

$client = new WipopClient($configuration);

$params = (new CreateChargeParams())
    ->method(ChargeMethod::CARD)
    ->amount(100.00)
    ->currency('EUR')
    ->description('Pago del pedido #123')
    ->productType(ProductType::PAYMENT_LINK)
    ->orderId(OrderId::fromString('1234ABCDEFGH'))
    ->terminal(new Terminal(1));

$charge = $client->chargeOperation()->create($params);
```

## Scripts de ejemplo

Los ejemplos ejecutables se encuentran en `docs/examples` y leen credenciales desde `.env`. Puedes
lanzarlos con `php docs/examples/<script>.php`.

- `card-charge.php` / `bizum-charge.php`: generán cargos/link de pago para tarjeta o Bizum.
- `checkout.php` / `checkoutCustomer.php`: crean checkouts para clientes anónimos o registrados.
- `preauth-confirm.php` / `preauth-cancel.php`: muestran el flujo de preautorizaciones.
- `payment-methods.php`: consulta los métodos de pago activos para tu comercio.
## Configuración

### Configuración básica

```php
use Wipop\Client\WipopClientConfiguration;
use Wipop\Client\Environment;

$config = new WipopClientConfiguration(
    Environment::SANDBOX, // Environment::PRODUCTION para producción
    'merchant-id',
    'secret-key'
);
```

### Configuración HTTP

```php
use Wipop\Client\WipopClientConfiguration;
use Wipop\Client\WipopClientHttpConfiguration;
use Wipop\Client\Environment;

$httpConfig = new WipopClientHttpConfiguration(10_000, 45_000);

$config = new WipopClientConfiguration(
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
use Wipop\Domain\ChargeMethod;
use Wipop\Operations\Charge\Params\CreateChargeParams;
use Wipop\Domain\Value\OrderId;
use Wipop\Domain\ProductType;
use Wipop\Domain\Value\Terminal;

$cardCharge = (new CreateChargeParams())
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
$bizumCharge = (new CreateChargeParams())
    ->method(ChargeMethod::BIZUM)
    ->amount(50.00)
    ->currency('EUR')
    ->description('Pago Bizum')
    ->orderId(OrderId::fromString('2345ABCDEFGH'))
    ->productType(ProductType::PAYMENT_LINK)
    ->terminal(new Terminal(1));

$response = $client->chargeOperation()->createCustomerCharge('customer-id', $bizumCharge);
```

### Confirmar / devolver / anular / capturar

```php
use Wipop\Operations\Charge\Params\ConfirmChargeParams;
use Wipop\Operations\Charge\Params\CaptureParams;
use Wipop\Operations\Charge\Params\RefundParams;
use Wipop\Operations\Charge\Params\ReversalParams;

$confirm = (new ConfirmChargeParams())->amount(75.00);
$client->chargeOperation()->confirm('transaction-id', $confirm);

$capture = (new CaptureParams())->amount(75.00);
$client->chargeOperation()->capture('transaction-id', $capture);

$refund = (new RefundParams())->amount(25.00);
$client->chargeOperation()->refund('transaction-id', $refund);

$reversal = (new ReversalParams())->reason('PRE_REVERSAL');
$client->chargeOperation()->reversal('transaction-id', $reversal);
```

### Tokenización, one-click, recurrentes, preautorizaciones

Aplica `useCof(true)` para tokenizar, `sourceId()` para cargos one-click, `postType(PostTypeMode::RECURRENT)` para recurrentes y `capture(false)` para preautorizar.

### Pagos recurrentes

Para crear pagos recurrentes basta con construir el payload de `chargeOperation()->create()` para marcar la operación como recurrente y, tras el primer cobro exitoso, reutilizar el identificador de
fuente (sourceId) que devuelve la API.

```php
use Wipop\Domain\ChargeMethod;
use Wipop\Operations\Charge\Params\CreateChargeParams;
use Wipop\Domain\PostType;
use Wipop\Domain\PostTypeMode;
use Wipop\Domain\Value\Terminal;

// Primer cargo: se tokeniza el medio de pago
$primerCargo = (new CreateChargeParams())
    ->method(ChargeMethod::CARD)
    ->amount(29.99)
    ->useCof(true)
    ->postType(new PostType(PostTypeMode::RECURRENT))
    ->capture(true)
    ->redirectUrl('https://miweb.test/return')
    ->terminal(new Terminal(1));

$respuestaInicial = $client->chargeOperation()->create($primerCargo);
$sourceId = $respuestaInicial->card?->id; // persiste para los siguientes ciclos

// Ciclos siguientes: se reutiliza el sourceId almacenado
$cargoRecurrente = (new CreateChargeParams())
    ->method(ChargeMethod::CARD)
    ->amount(29.99)
    ->sourceId($sourceId)
    ->postType(new PostType(PostTypeMode::RECURRENT))
    ->terminal(new Terminal(1));

$client->chargeOperation()->create($cargoRecurrente);

```
Operación recurrente

```php
use Wipop\Domain\ChargeMethod;
use Wipop\Operations\RecurrentPayment\Params\RecurrentPaymentParams;
use Wipop\Domain\Value\Terminal;

$recurrentParams = (new RecurrentPaymentParams())
    ->method(ChargeMethod::CARD)
    ->amount(29.99)
    ->terminal(new Terminal(1));

$client->recurrentPaymentOperation()->create($recurrentParams);
```

## Operaciones de checkout

```php
use Wipop\Operations\Checkout\Params\CheckoutParams;

$checkoutParams = (new CheckoutParams())
    ->amount(49.90)
    ->currency('EUR')
    ->description('Checkout de ejemplo')
    ->orderId(OrderId::fromString('3456ABCDEFGH'))
    ->productType(ProductType::PAYMENT_GATEWAY)
    ->terminal(new Terminal(1))
    ->redirectUrl('https://tu-sitio.com/success')
    ->sendEmail(true);

$checkout = $client->checkoutOperation()->createCheckout($checkoutParams);
```

### Checkout para un cliente existente

Si el cliente ya existe en Wipop, usa `createCustomerCheckout()` pasando su `publicId`.

```php
use Wipop\Domain\Input\Customer;

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

$checkout = $client->checkoutOperation()->createCustomerCheckout('cust_1234567890', $checkoutParams);
```

## Operaciones de merchant

Consulta los métodos de pago habilitados para un merchant y terminal concretos.

```php
use Wipop\Domain\ProductType;
use Wipop\Domain\Value\Terminal;

$metodos = $client
    ->merchantOperation()
    ->listPaymentMethods(ProductType::PAYMENT_GATEWAY, new Terminal(1));

// ['CARD', 'BIZUM', 'GOOGLE_PAY']
```

## Manejo de errores

```php
use Wipop\Exception\WipopException;

try {
    $charge = $client->chargeOperation()->create($params);
} catch (WipopException $exception) {
    // Maneja el error según código
}
```

## Referencia de API

- **WipopClient**: punto de entrada.
- **WipopClientConfiguration / WipopClientHttpConfiguration / Environment**: configuración del cliente.
- **ChargeOperation**: creación, confirmación, devolución, anulación y captura.
- **CheckoutOperation**: gestión de sesiones de checkout.
- **MerchantOperation**: consulta de metadatos del comercio (métodos de pago habilitados).
- **RecurrentPaymentOperation**: wrapper para crear cargos recurrentes.
- **CreateChargeParams / ConfirmChargeParams / CaptureParams / RefundParams / ReversalParams**.
- **CheckoutParams**.
- **Modelos de dominio**: `Charge`, `Checkout`, `Customer`, `Terminal`, `PaymentMethod`, `ChargeMethod`, `TransactionStatus`.


## Soporte

Para soporte y preguntas, por favor contacta al equipo de soporta o [crea un issue](https://docs.github.com/en/issues/tracking-your-work-with-issues/learning-about-issues/quickstart) en el repositorio del proyecto.
