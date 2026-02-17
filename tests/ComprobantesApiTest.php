<?php

namespace Bluetech\Sdk\Tests;

use Bluetech\Sdk\ApiClient;
use Bluetech\Sdk\Config;
use Bluetech\Sdk\Models\EmitComprobanteRequest;
use Bluetech\Sdk\Resources\ComprobantesApi;
use Bluetech\Sdk\Tests\Support\FakeHttpClient;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class ComprobantesApiTest extends TestCase
{
    public function testEmitirUsesEndpointAndBody(): void
    {
        $response = new Response(200, [], json_encode([
            '_Id' => 999,
            'CodRespuesta' => '00',
            'MensajeRespuesta' => 'OK',
            'IdComprobante' => 321,
            'TipoCfe' => 111,
            'Serie' => 'A',
            'Nro' => 15,
        ]));

        $httpClient = new FakeHttpClient([$response]);
        $factory = new HttpFactory();
        $client = new ApiClient(new Config('https://example.test'), $httpClient, $factory, $factory);
        $api = new ComprobantesApi($client);

        $req = EmitComprobanteRequest::fromArray([
            'idEmpresa' => 10,
            'codComercio' => '001',
            'codTerminal' => 'T1',
            'origen' => 'Api',
            'cfe' => [
                'idDoc' => [
                    'tipoCfe' => 111,
                    'fechaEmision' => '2026-02-12',
                    'formaPago' => 1,
                ],
                'totales' => [
                    'tipoMoneda' => 'UYU',
                    'montoTotal' => 122,
                    'montoPagar' => 122,
                ],
                'detalles' => [
                    [
                        'numeroLineaDetalle' => 1,
                        'indicadorFacturacion' => 1,
                        'nombreItem' => 'Servicio',
                        'cantidad' => 1,
                        'precioUnitario' => 100,
                        'montoItem' => 122,
                    ],
                ],
            ],
        ]);

        $resp = $api->emitir($req);

        $this->assertSame('00', $resp->CodRespuesta);
        $this->assertCount(1, $httpClient->requests);
        $request = $httpClient->requests[0];
        $this->assertSame('POST', $request->getMethod());
        $this->assertSame('/api/v1/comprobante/emitir', $request->getUri()->getPath());

        $payload = json_decode((string)$request->getBody(), true);
        $this->assertSame(10, $payload['idEmpresa']);
        $this->assertSame('001', $payload['codComercio']);
        $this->assertSame('T1', $payload['codTerminal']);
        $this->assertSame(111, $payload['cfe']['idDoc']['tipoCfe']);
    }
}

