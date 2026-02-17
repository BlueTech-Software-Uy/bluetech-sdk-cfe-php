<?php

namespace Bluetech\Sdk\Models;

class EmitComprobanteRequest extends Model
{
    public int $idEmpresa;
    public string $codComercio;
    public string $codTerminal;
    public string $adenda = '';
    public string $uuid = '';
    public string $destinatariosPdf = '';
    public string $origen = '3'; //API
    public int $esAppMovil = 0;
    public int $idComprobanteGuardado = 0;
    public int $idVendedor = 0;
    public ?int $idCaja = null;
    public CfePayload $cfe;

    public static function fromArray(array $data)
    {
        /** @var self $obj */
        $obj = new static();

        $obj->idEmpresa = isset($data['idEmpresa']) ? (int)$data['idEmpresa'] : 0;
        $obj->codComercio = isset($data['codComercio']) ? (string)$data['codComercio'] : '';
        $obj->codTerminal = isset($data['codTerminal']) ? (string)$data['codTerminal'] : '';
        $obj->adenda = isset($data['adenda']) ? (string)$data['adenda'] : '';
        $obj->uuid = isset($data['uuid']) ? (string)$data['uuid'] : '';
        $obj->destinatariosPdf = isset($data['destinatariosPdf']) ? (string)$data['destinatariosPdf'] : '';
        $obj->origen = isset($data['origen']) ? (string)$data['origen'] : '3';
        $obj->esAppMovil = isset($data['esAppMovil']) ? (int)$data['esAppMovil'] : 0;
        $obj->idComprobanteGuardado = isset($data['idComprobanteGuardado']) ? (int)$data['idComprobanteGuardado'] : 0;
        $obj->idVendedor = isset($data['idVendedor']) ? (int)$data['idVendedor'] : 0;
        $obj->idCaja = array_key_exists('idCaja', $data) && $data['idCaja'] !== null ? (int)$data['idCaja'] : null;

        $cfe = isset($data['cfe']) && is_array($data['cfe']) ? $data['cfe'] : [];
        $obj->cfe = CfePayload::fromArray($cfe);

        return $obj;
    }

    public function toArray(): array
    {
        return [
            'idEmpresa' => $this->idEmpresa,
            'codComercio' => $this->codComercio,
            'codTerminal' => $this->codTerminal,
            'adenda' => $this->adenda,
            'uuid' => $this->uuid,
            'destinatariosPdf' => $this->destinatariosPdf,
            'origen' => $this->origen,
            'esAppMovil' => $this->esAppMovil,
            'idComprobanteGuardado' => $this->idComprobanteGuardado,
            'idVendedor' => $this->idVendedor,
            'idCaja' => $this->idCaja,
            'cfe' => $this->cfe->toArray(),
        ];
    }
}

