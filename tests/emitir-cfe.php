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
    $baseUrl = getenv('BT_BASE_URL') ?: 'https://test.facture.uy/cfe';
    //$baseUrl = "http://localhost/cfe"; // Para pruebas locales con el mock server
    $username = getenv('BT_USER') ?: 'dev@bluetech'; //
    $secret = getenv('BT_SECRET') ?: 'a81e364fbe7047fa286b8079a316f26f110ebf9f5fe4b86315e9d8ecb627776c';


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

    // Desde 2026-02-17 se puede omitir cfe.emisor y cfe.totales:
    // la API completa emisor por idEmpresa/codComercio/fechaEmision
    // y calcula totales desde detalles.
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
