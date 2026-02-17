<?php

namespace Bluetech\Sdk\Resources;

use Bluetech\Sdk\ApiClient;
use Bluetech\Sdk\Models\EmitComprobanteRequest;
use Bluetech\Sdk\Models\EmitComprobanteResponse;

class ComprobantesApi
{
    private ApiClient $client;

    public function __construct(ApiClient $client)
    {
        $this->client = $client;
    }

    public function emitir(EmitComprobanteRequest $request): EmitComprobanteResponse
    {
        $data = $this->client->request(
            'POST',
            '/api/v1/comprobante/emitir',
            [],
            $request->toArray()
        );

        return EmitComprobanteResponse::fromArray($data);
    }
}

