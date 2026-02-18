# Comprobantes (emitir)

Recurso dedicado: `comprobantes()`

Metodo disponible:

- `emitir(EmitComprobanteRequest $request): EmitComprobanteResponse`

Endpoint API usado internamente:

- `POST /api/v1/comprobante/emitir`

## Ejemplo minimo

```php
use Bluetech\Sdk\Models\EmitComprobanteRequest;

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
        'receptor' => [
            'idCliente' => 123,
        ],
        'detalles' => [
            [
                'numeroLineaDetalle' => 1,
                'indicadorFacturacion' => 3,
                'nombreItem' => 'Servicio',
                'cantidad' => 1,
                'precioUnitario' => 122,
                'montoItem' => 122,
            ],
        ],
    ],
]);

$resp = $sdk->comprobantes()->emitir($request);
echo $resp->CodRespuesta; // 00
echo $resp->Serie . '-' . $resp->Nro;
```

## Novedades API (17-02-2026)

- `cfe.totales` es opcional: si no se envia (o llega vacio), la API lo calcula desde `cfe.detalles`.
- `cfe.emisor` es opcional: si no se envia (o no tiene `ruc`), la API lo completa automaticamente segun `idEmpresa + codComercio` y `idDoc.fechaEmision`.

Esto habilita requests mas cortos en SDK, manteniendo compatibilidad con integraciones que siguen enviando `emisor` y `totales`.

## Campos del request soportados por modelo

`EmitComprobanteRequest`:

- `idEmpresa` (int, requerido)
- `codComercio` (string, requerido)
- `codTerminal` (string, requerido)
- `adenda` (string, opcional)
- `uuid` (string, opcional)
- `destinatariosPdf` (string, opcional)
- `cfe` (`CfePayload`, requerido)

`CfePayload` permite enviar:

- `idDoc` (array)
- `emisor` (array, opcional)
- `receptor` (array)
- `totales` (array, opcional)
- `detalles` (array de lineas)
- `referencias` (array)
- `descuentosRecargosGlobales` (array)
- `mediosPago` (array)
- `subTotalesInfo` (array)
- `complementoFiscal` (array)

El SDK no impone validaciones fiscales de dominio; esas validaciones ocurren en la API.

## Respuesta

`EmitComprobanteResponse` expone:

- `_Id`
- `CodRespuesta`
- `MensajeRespuesta`
- `IdComprobante`
- `TipoCfe`
- `Serie`
- `Nro`
- `IdCAE`
- `NroInicalCAE`
- `NroFinalCAE`
- `DatosQR`
- `CodSeguridad`
- `XmlFirmado`
