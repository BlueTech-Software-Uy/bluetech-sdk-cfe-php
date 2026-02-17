# Productos

```php
$variants = $sdk->products()->variants([
    'idEmpresa' => 10,
    'start' => 0,
    'length' => 20,
]);

$search = $sdk->products()->search([
    'idEmpresa' => 10,
    'producto' => 'plan',
]);

$searchSimple = $sdk->products()->searchSimple([
    'idEmpresa' => 10,
    'producto' => 'plan',
]);

$variant = $sdk->products()->getVariantById(123);
```
