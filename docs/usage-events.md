# Usage events

Envia eventos de consumo externos de forma idempotente.

```php
use Bluetech\Sdk\Models\UsageEvent;

$event = UsageEvent::fromArray([
    'contractId' => 123,
    'eventId' => 'evt-1001',
    'occurredAt' => '2026-02-10T15:30:00Z',
    'quantity' => 10,
    'unit' => 'call',
    'metric' => 'API_CALL',
    'description' => 'Llamadas API'
]);

$response = $sdk->usage()->createUsageEvents($event, 'idem-evt-1001');
```

Bulk:

```php
$sdk->usage()->createUsageEvents([
    ['contractId' => 123, 'eventId' => 'evt-1', 'occurredAt' => '2026-02-10T15:30:00Z', 'quantity' => 1, 'unit' => 'call', 'metric' => 'API_CALL'],
    ['contractId' => 123, 'eventId' => 'evt-2', 'occurredAt' => '2026-02-10T15:31:00Z', 'quantity' => 2, 'unit' => 'call', 'metric' => 'API_CALL'],
]);
```
