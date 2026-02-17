# Webhooks

```php
use Bluetech\Sdk\Models\WebhookCreateRequest;

$webhook = WebhookCreateRequest::fromArray([
    'url' => 'https://miapp.com/webhooks',
    'events' => ['contract.status.changed'],
    'enabled' => true,
]);

$sdk->webhooks()->create($webhook, 10);
```
