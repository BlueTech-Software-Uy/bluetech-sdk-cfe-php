<?php

namespace Bluetech\Sdk\Models;

class WebhookCreateRequest extends Model
{
    public string $url;
    /** @var string[] */
    public array $events = [];
    public ?string $name = null;
    public ?bool $enabled = null;
    public ?string $secret = null;
    public ?array $headers = null;
}
