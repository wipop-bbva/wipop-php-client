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

## Flujo de calidad del código

- PHPStan (`composer lint:phpstan`) analiza el código en busca de errores de tipos y comportamiento. GrumPHP lo ejecuta automáticamente en cada commit.
- php-cs-fixer (`composer fix:php`) formatea el código según `.php-cs-fixer.dist.php`. GrumPHP comprueba en modo dry-run que los archivos están alineados antes de permitir el commit.
- GrumPHP instala los hooks de Git y coordina los chequeos anteriores para que todos los commits pasen por las mismas validaciones.
- PHPCS (`composer lint:phpcs`) herramienta opcional para análisis puntual.No se ejecuta automáticamente en los hooks.

## Licencia

Este proyecto está bajo la licencia MIT.

