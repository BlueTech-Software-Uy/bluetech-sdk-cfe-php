# Bluetech PHP SDK

SDK PHP oficial para consumir la API REST de BlueTech CFE.

## Requisitos

- PHP >= 7.4
- PSR-18 (HTTP Client), PSR-7 (Message), PSR-17 (Factories)

El SDK es agnostico al HTTP client.
La `baseUrl` debe ser el host, por ejemplo: `https://cfetesting.bluetechsoftware.cloud/cfe`.

## Instalacion

```bash
composer require bluetech/bluetech-sdk-php
```

## Inicio rapido (Emitir comprobante)

```php
<?php

use Bluetech\Sdk\Client;
use Bluetech\Sdk\Config;
use Bluetech\Sdk\Models\EmitComprobanteRequest;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\HttpFactory;

$httpClient = new GuzzleClient();
$httpFactory = new HttpFactory();
$config = new Config('https://cfetesting.bluetechsoftware.cloud/cfe');

$sdk = new Client($config, $httpClient, $httpFactory, $httpFactory);
$sdk->auth()->loginAndSetToken('usuario', 'secret');

$request = EmitComprobanteRequest::fromArray([
    'idEmpresa' => 10,
    'codComercio' => '001',
    'codTerminal' => 'T1',
    'cfe' => [
        'idDoc' => [
            'tipoCfe' => 111,
            'fechaEmision' => '2026-02-12',
            'formaPago' => 1,
        ],
        'emisor' => [
            //datos del emisor
        ],
        'receptor' => [
            //datos del receptor
        ],
        'totales' => [
            'tipoMoneda' => 'UYU',
            'montoTotal' => 122,
            'montoPagar' => 122,
            'cantidadLineasDetalle' => 1,
        ],
        'detalles' => [
            [
                'numeroLineaDetalle' => 1,
                'indicadorFacturacion' => 1,
                'nombreItem' => 'Plan mensual',
                'cantidad' => 1,
                'precioUnitario' => 100,
                'montoItem' => 122,
            ],
        ],
    ],
]);

$emitido = $sdk->comprobantes()->emitir($request);
echo $emitido->CodRespuesta . PHP_EOL;
echo $emitido->TipoCfe . '-' . $emitido->Serie . '-' . $emitido->Nro . PHP_EOL;
```

## Script completo listo para ejecutar

Archivo:

- `tests/emitir-cfe.php`

Que incluye:

- autenticacion
- armado de request tipado
- emision
- manejo de excepciones por tipo (`401/403/404/409/422/429/5xx`)

### Ejecutar

1. Instalar dependencias:

```bash
cd bluetech-sdk-php
composer install
```

2. Configurar entorno:

```bash
export BT_BASE_URL="https://cfetesting.bluetechsoftware.cloud/cfe"
export BT_USER="tu_usuario"
export BT_SECRET="tu_secret"
```

3. Ejecutar script:

```bash
php tests/emitir-cfe.php
```

El script guarda token/refresh token en:

- `tests/.tokens.json`

## Autenticacion

Bearer token:

```php
$config->setToken('nuevo-token');
```

Login:

```php
$tokens = $sdk->auth()->login('usuario', 'secret');
```

Login + set token:

```php
$tokens = $sdk->auth()->loginAndSetToken('usuario', 'secret');
```

Refresh:

```php
$tokens = $sdk->auth()->exchangeRefreshToken($tokens->refresh_token);
```

Si hay `refresh_token`, el SDK intenta refrescar automaticamente ante `401`.

## Modulos del SDK

### 1) Comprobantes (CFE)

- `comprobantes()->emitir(EmitComprobanteRequest $request): EmitComprobanteResponse`

### 2) Suscripciones y facturacion recurrente

```php
// Contratos
$list = $sdk->subscriptions()->listContracts(['page' => 1, 'per_page' => 50]);
$contract = $sdk->subscriptions()->getContractById(123);
$sdk->subscriptions()->activateContract(123);
$sdk->subscriptions()->deactivateContract(123);

// Facturacion recurrente
$catalogs = $sdk->recurringBilling()->catalogs();
$preview = $sdk->recurringBilling()->preview(123);
$result = $sdk->recurringBilling()->execute(123, 'idem-123');
$logs = $sdk->recurringBilling()->history(123);
```

### 3) Usage billing

```php
use Bluetech\Sdk\Models\UsageEvent;

$event = UsageEvent::fromArray([
    'contractId' => 123,
    'eventId' => 'evt-0001',
    'occurredAt' => '2026-02-01T12:00:00Z',
    'quantity' => 2,
    'unit' => 'GB',
    'metric' => 'storage',
]);

$sdk->usage()->createUsageEvents($event, 'idem-evt-0001');

$usage = $sdk->usage()->getContractUsage(123, '2026-02-01', '2026-02-28', [
    'group_by' => 'day',
    'page' => 1,
    'per_page' => 50,
]);
```

### 4) Catalogos y recursos maestros

```php
$companies = $sdk->users()->companies();
$currencies = $sdk->currencies()->all();
$customers = $sdk->customers()->search(['idEmpresa' => 10, 'cliente' => 'acme']);
$branches = $sdk->branches()->all(['idEmpresa' => 10]);
$emissionPoints = $sdk->emissionPoints()->byBranch(1, ['idEmpresa' => 10]);
$products = $sdk->products()->search(['idEmpresa' => 10, 'producto' => 'plan']);
```

### 5) Price books y webhooks

```php
$priceBook = $sdk->priceBooks()->create([
    'name' => 'Standard',
    'currency' => 'UYU',
    'items' => [
        ['sku' => 'SKU-001', 'name' => 'Plan Base', 'price' => 100],
    ],
], 10);

$versions = $sdk->priceBooks()->versions($priceBook->id);

$webhook = $sdk->webhooks()->create([
    'url' => 'https://miapp.com/webhooks',
    'events' => ['recurring.invoice.created'],
    'enabled' => true,
], 10);
```

## Manejo de errores

Respuestas no 2xx lanzan excepciones:

- `UnauthorizedException` (401)
- `ForbiddenException` (403)
- `NotFoundException` (404)
- `IdempotencyConflictException` (409)
- `ValidationException` (400/422)
- `RateLimitException` (429)
- `ServerException` (5xx)

Todas extienden `ApiException` y exponen:

- `statusCode`
- `errorCode`
- `message`
- `details`
- `requestId`

## Reintentos

Configurable en `Config`:

```php
$config->setRetryMax(3);
$config->setRetryBackoffMs(500);
```

## Documentacion completa

- `docs/README.md` (indice navegable)
- `docs/comprobantes.md` (emitir CFE)
- `docs/use-cases.md` (casos de uso E2E)

## Calidad

```bash
composer install
vendor/bin/phpunit
vendor/bin/phpstan analyse -c phpstan.neon
```

## Licencia

MIT

