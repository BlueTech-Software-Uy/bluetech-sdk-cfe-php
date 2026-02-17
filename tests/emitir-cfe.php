<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Bluetech\Sdk\Auth\FileTokenStore;
use Bluetech\Sdk\Client;
use Bluetech\Sdk\Config;
use Bluetech\Sdk\Exceptions\ApiException;
use Bluetech\Sdk\Exceptions\ForbiddenException;
use Bluetech\Sdk\Exceptions\IdempotencyConflictException;
use Bluetech\Sdk\Exceptions\NotFoundException;
use Bluetech\Sdk\Exceptions\RateLimitException;
use Bluetech\Sdk\Exceptions\ServerException;
use Bluetech\Sdk\Exceptions\UnauthorizedException;
use Bluetech\Sdk\Exceptions\ValidationException;
use Bluetech\Sdk\Models\EmitComprobanteRequest;
use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Psr7\HttpFactory;

function logLine(string $message): void
{
    fwrite(STDOUT, $message . PHP_EOL);
}

function logApiException(string $title, ApiException $e): void
{
    logLine($title . ': ' . $e->getMessage());
    logLine('status=' . $e->getStatusCode() . ' code=' . ($e->getErrorCode() ?? 'n/a') . ' requestId=' . ($e->getRequestId() ?? 'n/a'));
    if (!empty($e->getDetails())) {
        logLine('details=' . json_encode($e->getDetails(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
}

try {
    $baseUrl = getenv('BT_BASE_URL') ?: 'https://cfetesting.bluetechsoftware.cloud/cfe';
    $username = getenv('BT_USER') ?: 'demoavanzado';
    $secret = getenv('BT_SECRET') ?: '9978f03f445e3c999b723a91f90e858d53fd3f97049e504d0ddb941c330599f1';

    $httpClient = new GuzzleHttpClient([
        'timeout' => 30,
        'connect_timeout' => 10,
    ]);
    $httpFactory = new HttpFactory();
    $config = new Config($baseUrl);
    $config->setTokenStore(new FileTokenStore(__DIR__ . '/.tokens.json'));

    $sdk = new Client($config, $httpClient, $httpFactory, $httpFactory);

    logLine('Autenticando usuario...');
    $sdk->auth()->loginAndSetToken($username, $secret);

    $request = EmitComprobanteRequest::fromArray([
        'idEmpresa' => 2,
        'codComercio' => 'blue001',
        'codTerminal' => 'bl-01',
        'cfe' => [
            'idDoc' => [
                'tipoCfe' => 111,
                'fechaEmision' => '2026-02-12',
                'formaPago' => 1,
            ],
            'emisor' => [
                'ruc' => '219879740012',
                'razonSocial' => 'Empresa de Prueba S.A.',
                'nombreComercial' => 'Empresa de Prueba',
                'giro' => 'Servicios',
                'telefono' => '12345678',
                'segundoTelefono' => '87654321',
                'email' => 'test@localhost',
                'sucursal' => 'Sucursal Principal',
                'codigoDgiSucursal' => 1,
                'domicilioFiscal' => 'Calle Falsa 123',
                'ciudad' => 'Montevideo',
                'departamento' => 'Montevideo',
                'informacionAdicional' => 'Informacion adicional del emisor'
            ],
            'receptor' => [
                'tipoDocumento' => 2,
                'documento' => '170294150010',
                'documentoExt' => null,
                'codigoPais' => 'UY',
                'razonSocial' => 'Cliente de Prueba',
                'direccion' => 'Avenida Siempre Viva 742',
                'ciudad' => 'Montevideo',
                'departamento' => 'Montevideo',
                'pais' => 'Uruguay',
                'codigoPostal' => null,
                'informacionAdicional' => 'Informacion adicional del receptor',
                'lugarEntrega' => 'GLN123',
                'idCompraCliente' => '12345',
            ],
            'totales' => [
                'tipoMoneda' => 'UYU',
                'montoNoGravado' => 0,
                'montoExportacionYAsim' => 0,
                'montoImpuestoPercibido' => 0,
                'montoIvaSuspenso' => 0,
                'montoNetoIvaTasaMinima' => 0,
                'montoNetoIvaTasaBasica' => 100,
                'montoNetoIvaOtraTasa' => 0,
                'tasaIvaTasaMinima' => 10,
                'tasaIvaTasaBasica' => 22,
                'montoIvaTasaMinima' => 0,
                'montoIvaTasaBasica' => 22,
                'montoIvaOtraTasa' => 0,
                'montoNoFacturable' => 0,
                'montoTotal' => 122,
                'montoPagar' => 122,
                'cantidadLineasDetalle' => 1,
            ],
            'detalles' => [
                [
                    'numeroLineaDetalle' => 1,
                    'indicadorFacturacion' => 3,
                    'nombreItem' => 'Servicio',
                    'unidadMedida' => 'N/A',
                    'cantidad' => 1,
                    'precioUnitario' => 100,
                    'montoItem' => 100,
                ],
            ],
        ],
    ]);

    logLine('Emitiendo comprobante...');
    $resp = $sdk->comprobantes()->emitir($request);

    logLine('Emitido OK');
    logLine('CodRespuesta: ' . (string)$resp->CodRespuesta);
    logLine('Mensaje: ' . (string)$resp->MensajeRespuesta);
    logLine('Comprobante: ' . (string)$resp->TipoCfe . '-' . (string)$resp->Serie . '-' . (string)$resp->Nro);
    logLine('IdComprobante: ' . (string)$resp->IdComprobante);
    exit(0);
} catch (UnauthorizedException $e) {
    logApiException('Auth fallida', $e);
    exit(1);
} catch (ValidationException $e) {
    logApiException('Request invalido', $e);
    exit(2);
} catch (ForbiddenException $e) {
    logApiException('Sin permisos', $e);
    exit(3);
} catch (NotFoundException $e) {
    logApiException('No encontrado', $e);
    exit(4);
} catch (RateLimitException $e) {
    logApiException('Rate limit', $e);
    exit(5);
} catch (IdempotencyConflictException $e) {
    logApiException('Conflicto de idempotencia', $e);
    exit(6);
} catch (ServerException $e) {
    logApiException('Error servidor', $e);
    exit(7);
} catch (ApiException $e) {
    logApiException('Error API', $e);
    exit(8);
} catch (Throwable $e) {
    logLine('Error inesperado: ' . $e->getMessage());
    logLine($e->getTraceAsString());
    exit(99);
}
