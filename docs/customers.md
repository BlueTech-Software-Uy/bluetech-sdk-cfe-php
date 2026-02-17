# Clientes

```php
$customers = $sdk->customers()->all([
    'idEmpresa' => 10,
    'start' => 0,
    'length' => 20,
]);

$search = $sdk->customers()->search([
    'idEmpresa' => 10,
    'cliente' => 'acme',
]);

$customer = $sdk->customers()->getById(123);
```
