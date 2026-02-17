# Uso agregado por periodo

```php
$usage = $sdk->usage()->getContractUsage(123, '2026-02-01', '2026-02-28', [
    'group_by' => 'day',
    'page' => 1,
    'per_page' => 50,
]);
```
