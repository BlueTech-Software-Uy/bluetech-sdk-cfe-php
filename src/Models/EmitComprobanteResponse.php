<?php

namespace Bluetech\Sdk\Models;

class EmitComprobanteResponse extends Model
{
    /** @var int|string|null */
    public $_Id = null;
    public ?string $CodRespuesta = null;
    public ?string $MensajeRespuesta = null;
    public ?int $IdComprobante = null;
    public ?int $TipoCfe = null;
    public ?string $Serie = null;
    /** @var int|string|null */
    public $Nro = null;
    /** @var string|int|null */
    public $IdCAE = null;
    /** @var string|int|null */
    public $NroInicalCAE = null;
    /** @var string|int|null */
    public $NroFinalCAE = null;
    public ?string $DatosQR = null;
    public ?string $CodSeguridad = null;
    public ?string $XmlFirmado = null;
}

