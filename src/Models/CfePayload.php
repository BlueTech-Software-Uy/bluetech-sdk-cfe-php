<?php

namespace Bluetech\Sdk\Models;

class CfePayload extends Model
{
    /** @var array<string, mixed> */
    public array $idDoc = [];
    /** @var array<string, mixed> */
    public array $emisor = [];
    /** @var array<string, mixed> */
    public array $receptor = [];
    /** @var array<string, mixed> */
    public array $totales = [];
    /** @var array<int, array<string, mixed>> */
    public array $detalles = [];
    /** @var array<int, array<string, mixed>> */
    public array $referencias = [];
    /** @var array<int, array<string, mixed>> */
    public array $descuentosRecargosGlobales = [];
    /** @var array<int, array<string, mixed>> */
    public array $mediosPago = [];
    /** @var array<int, array<string, mixed>> */
    public array $subTotalesInfo = [];
    /** @var array<string, mixed> */
    public array $complementoFiscal = [];
}

