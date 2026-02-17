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
                'nombreItem' => 'Servicio',
                'cantidad' => 1,
                'precioUnitario' => 100,
                'montoItem' => 100,
            ],
        ],
    ],
]);

$resp = $sdk->comprobantes()->emitir($request);
echo $resp->CodRespuesta; // 00
echo $resp->Serie . '-' . $resp->Nro;
```

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
- `emisor` (array)
- `receptor` (array)
- `totales` (array)
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

