# Estado de contrato

```php
$state = $sdk->subscriptions()->contractState(123);

if ($state->usageStatus === 'LIMIT_REACHED') {
    // cortar acceso en sistema externo
}
```
