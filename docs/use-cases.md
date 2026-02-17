# Casos de uso

## Flujo completo end-to-end

El siguiente ejemplo cubre autenticacion, listas de precios, creacion de contrato, usage events,
estado, uso agregado, historial de estado y webhooks. Incluye idempotencia y reintentos.

```php
<?php

declare(strict_types=1);

use Bluetech\Sdk\Auth\FileTokenStore;
use Bluetech\Sdk\Client;
use Bluetech\Sdk\Config;
use Bluetech\Sdk\Exceptions\IdempotencyConflictException;
use Bluetech\Sdk\Exceptions\RateLimitException;
use Bluetech\Sdk\Exceptions\ValidationException;
use Bluetech\Sdk\Exceptions\UnauthorizedException;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\HttpFactory;

// 1) Bootstrap del SDK
$httpClient = new GuzzleClient();
$httpFactory = new HttpFactory();
$config = new Config('https://ambiente.bluetechsoftware.cloud/cfe/api/v1');
$config->setTokenStore(new FileTokenStore(__DIR__ . '/.tokens.json'));

$sdk = new Client($config, $httpClient, $httpFactory, $httpFactory);

// 2) Autenticacion (sin pasos manuales)
try {
    $sdk->auth()->loginAndSetToken('usuario', 'secret');
} catch (UnauthorizedException $e) {
    echo "Auth fallida: " . $e->getMessage();
    exit(1);
}

// 3) Crear lista de precios + version activa
$priceBook = $sdk->priceBooks()->createPriceBook([
    'name' => 'PB SaaS 2026',
    'description' => 'Lista de precios principal',
    'currency' => 'USD',
    'effectiveFrom' => '2026-02-01',
    'items' => [
        [
            'sku' => 'API_CALL',
            'name' => 'API Calls',
            'price' => 0.02,
            'unit' => 'call',
            'taxRate' => 0,
        ],
    ],
], 10);

$versions = $sdk->priceBooks()->versions($priceBook['id']);
$activeVersionId = $versions['data'][0]['id'] ?? null;

// 4) Resolver IDs requeridos antes de crear contrato
// catalogs(): solo enums de facturacion recurrente
$catalogs = $sdk->recurringBilling()->catalogs();
// recursos dedicados por controller
$companies = $sdk->users()->companies();
$currencies = $sdk->currencies()->all();

$idEmpresa = (int)($companies[0]->Id ?? $companies[0]['Id'] ?? 0);
if ($idEmpresa <= 0) {
    throw new RuntimeException('No hay empresas disponibles para el usuario autenticado.');
}

$moneda = strtoupper((string)($currencies[0]->Abreviacion ?? $currencies[0]['Abreviacion'] ?? 'USD'));
$frecuencia = (string)($catalogs['frecuencias'][0]['value'] ?? 'mensual');
$formaPago = (int)($catalogs['formasPago'][0]['value'] ?? 1);
$vencimientoTipo = (string)($catalogs['vencimientoTipos'][0]['value'] ?? 'dias');

// IDs funcionales: cliente, sucursal, terminal y producto variante
$clientes = $sdk->customers()->search(['idEmpresa' => $idEmpresa, 'cliente' => 'ACME']);
$clientes = empty($clientes['data']) ? $sdk->customers()->all(['idEmpresa' => $idEmpresa, 'start' => 0, 'length' => 1]) : $clientes;
$idCliente = (int)($clientes['data'][0]['Id'] ?? $clientes['data'][0]['id'] ?? 0);
if ($idCliente <= 0) {
    throw new RuntimeException('No se encontro un cliente para la empresa seleccionada.');
}

$sucursales = $sdk->branches()->all(['idEmpresa' => $idEmpresa]);
$idSucursal = (int)($sucursales[0]['Id'] ?? 0);
if ($idSucursal <= 0) {
    throw new RuntimeException('No se encontro una sucursal para la empresa seleccionada.');
}

$terminales = $sdk->emissionPoints()->byBranch($idSucursal, ['idEmpresa' => $idEmpresa]);
$idTerminal = (int)($terminales[0]['Id'] ?? 0);
if ($idTerminal <= 0) {
    throw new RuntimeException('No se encontro un punto de emision para la sucursal seleccionada.');
}

$variantes = $sdk->products()->search(['idEmpresa' => $idEmpresa, 'producto' => 'PLAN']);
$variantes = empty($variantes['data']) ? $sdk->products()->variants(['idEmpresa' => $idEmpresa, 'start' => 0, 'length' => 1]) : $variantes;
$idProductoVariante = (int)($variantes['data'][0]['Id'] ?? $variantes['data'][0]['id'] ?? $variantes[0]['Id'] ?? 0);
if ($idProductoVariante <= 0) {
    throw new RuntimeException('No se encontro un producto variante para la empresa seleccionada.');
}

// 5) Crear contrato / planificacion recurrente con todos los parametros posibles

$contract = $sdk->subscriptions()->createContract([
    // Identidad
    'idEmpresa' => $idEmpresa,
    'IdCliente' => $idCliente,
    'ExternalRef' => 'cliente-001',

    // Planificacion
    'Frecuencia' => $frecuencia,
    'FechaProxima' => '2026-03-01',
    'Activo' => 1,
    'PausaDesde' => null,
    'PausaHasta' => null,
    'Observaciones' => 'Contrato principal',

    // Facturacion
    'Moneda' => $moneda,
    'Sucursal' => $idSucursal,
    'Terminal' => $idTerminal,
    'formaPago' => $formaPago,
    'VencimientoTipo' => $vencimientoTipo,
    'VencimientoValor' => 10,

    // Detalles (items a facturar)
    'Detalles' => [
        [
            'IdProducto' => $idProductoVariante,
            'DescripcionTemplate' => 'Suscripcion base',
            'Cantidad' => 1,
            'Precio' => 100,
            'Descuento' => 0,
        ],
    ],

    // Cambio de plan / prorrateo (opcionales)
    'CambioPlan' => 0,
    'ProrrateoModo' => null, // immediate | next
    'AjusteTipoDoc' => null, // factura | nc | nd
    'FechaEfectivaModo' => null, // hoy | proxima | manual
    'CambioPlanFecha' => null,
    'IdProductoVarianteAjuste' => null,

    // Consumo (usage billing)
    'IdPriceBook' => $priceBook['id'],
    'IdPriceBookVersion' => $activeVersionId,
    'UsageServices' => [
        [
            'service_code' => 'API_CALL',
            'unit' => 'call',
            'sku' => 'API_CALL',
            'pricing_model' => 'included_overage',
            'included' => 1000,
            'overage_price' => 0.02,
            'soft_limit' => 1200,
            'hard_limit' => 1500,
        ],
    ],
]);

$contractId = $contract['Id'] ?? $contract['id'] ?? null;
if (!$contractId) {
    throw new RuntimeException('No se pudo crear contrato.');
}

// 6) Enviar usage events (idempotentes) con reintento
$events = [
    [
        'external_id' => 'evt-1001',
        'contract_ref' => 'cliente-001',
        'service_code' => 'API_CALL',
        'quantity' => 1,
        'event_time' => '2026-02-11T12:00:00Z',
        'metadata' => ['source' => 'backend'],
    ],
    [
        'external_id' => 'evt-1002',
        'contract_ref' => 'cliente-001',
        'service_code' => 'API_CALL',
        'quantity' => 1,
        'event_time' => '2026-02-11T12:01:00Z',
        'metadata' => ['source' => 'backend'],
    ],
];

foreach ($events as $event) {
    $attempts = 0;
    $maxAttempts = 3;
    while (true) {
        try {
            $sdk->usage()->createUsageEvent($event, $event['external_id']);
            break;
        } catch (IdempotencyConflictException $e) {
            // Ya procesado
            break;
        } catch (RateLimitException $e) {
            $attempts++;
            if ($attempts >= $maxAttempts) {
                throw $e;
            }
            usleep(300000);
            continue;
        } catch (ValidationException $e) {
            error_log("Evento invalido: " . $e->getMessage());
            break;
        }
    }
}

// 7) Consultar estado del contrato
$state = $sdk->subscriptions()->contractState($contractId);
// $state->status / $state->usageStatus / $state->usageReasons

// 8) Uso agregado por periodo
$usage = $sdk->subscriptions()->getContractUsage($contractId, [
    'start' => '2026-02-01',
    'end' => '2026-02-29',
]);

// 9) Historial de estado
$history = $sdk->subscriptions()->contractStatusHistory($contractId, [
    'start' => '2026-02-01',
    'end' => '2026-02-29',
]);

// 10) Registrar webhook para eventos operativos
$webhook = $sdk->webhooks()->createWebhook([
    'url' => 'https://mi-sistema.com/webhooks/usage',
    'events' => ['CONTRACT_STATUS_CHANGED', 'USAGE_LIMIT_REACHED'],
    'secret' => 'mi-secreto',
    'enabled' => true,
]);

// 11) Preview + ejecutar facturacion recurrente
$preview = $sdk->recurringBilling()->preview([
    'IdFacturacionRecurrente' => $contractId,
    'Fecha' => '2026-02-29',
]);

$execution = $sdk->recurringBilling()->execute([
    'IdFacturacionRecurrente' => $contractId,
    'Fecha' => '2026-02-29',
]);
```

## Casos puntuales

### 1) Recibir consumo externo (idempotente)

```php
$sdk->usage()->createUsageEvent([
    'external_id' => 'evt-1001',
    'contract_ref' => 'cliente-001',
    'service_code' => 'API_CALL',
    'quantity' => 10,
    'event_time' => '2026-02-10T15:30:00Z',
    'metadata' => ['source' => 'backend'],
], 'evt-1001');
```

### 2) Monitor de estado operativo

```php
$state = $sdk->subscriptions()->contractState(123);
if ($state->usageStatus === 'LIMIT_REACHED') {
    // cortar acceso en el sistema externo
}
```

### 3) Price book + versionado

```php
$priceBook = $sdk->priceBooks()->createPriceBook([
    'name' => 'SaaS Default',
    'currency' => 'UYU',
    'items' => [
        ['sku' => 'API_CALL', 'price' => 0.02, 'unit' => 'call'],
    ],
], 10);

$versions = $sdk->priceBooks()->versions($priceBook['id']);
```

### 4) Historial de estado

```php
$history = $sdk->subscriptions()->contractStatusHistory(123);
foreach ($history->data as $entry) {
    // $entry->statusFrom, $entry->statusTo, $entry->createdAt
}
```
