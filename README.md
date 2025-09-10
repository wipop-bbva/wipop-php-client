# Wipop Payment PHP

Este proyecto es una librería en PHP para integrar pagos con la plataforma Wipop. Proporciona una serie de clases y servicios para gestionar pagos con tarjeta, pagos recurrentes, procesos de checkout, clientes y utilidades relacionadas.

## Estructura del proyecto

- `src/` - Código fuente principal organizado por módulos:
  - `CardPayment/` - Gestión de pagos con tarjeta.
  - `Checkout/` - Procesos de checkout.
  - `Client/` - Configuración y cliente HTTP para la comunicación con la API de Wipop.
  - `Customer/` - Gestión de clientes y direcciones.
  - `RecurrentPayment/` - Pagos recurrentes.
  - `Utils/` - Utilidades como estados, monedas, idiomas, etc.
- `vendor/` - Dependencias gestionadas por Composer.
- `composer.json` y `composer.lock` - Configuración y bloqueo de dependencias.

## Instalación

1. Clona el repositorio:
   ```bash
   git clone <url-del-repositorio>
   ```
2. Instala las dependencias con Composer:
   ```bash
   composer install
   ```

## Uso básico

Puedes instanciar el cliente principal y utilizar los servicios para realizar pagos, gestionar clientes, etc. Consulta la documentación de cada clase en el código fuente para más detalles.

```php
use Wipop\Client\WipopClient;
use Wipop\CardPayment\CardPaymentService;

$client = new WipopClient($config);
$cardService = new CardPaymentService($client);
// ...
```

## Contribuir

Las contribuciones son bienvenidas. Por favor, abre un issue o envía un pull request para sugerencias o mejoras.

## Licencia

Este proyecto está bajo la licencia MIT.

