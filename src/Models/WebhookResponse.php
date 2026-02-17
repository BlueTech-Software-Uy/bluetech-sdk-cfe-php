<?php

namespace Bluetech\Sdk\Models;

class WebhookResponse extends Model
{
    public int $id;
    public string $url;
    /** @var string[] */
    public array $events = [];
    public bool $enabled = true;
}
