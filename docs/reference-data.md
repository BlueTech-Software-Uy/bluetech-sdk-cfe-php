# Datos de referencia

Empresas y monedas no forman parte del endpoint de catalogos de facturacion recurrente.

```php
$companies = $sdk->users()->companies();   // GET /api/v1/usuario/empresas
$currencies = $sdk->currencies()->all();   // GET /api/v1/monedas/todas
```

Recursos dedicados para IDs operativos:

```php
$customers = $sdk->customers()->all(['idEmpresa' => 10, 'start' => 0, 'length' => 20]);
$customersSearch = $sdk->customers()->search(['idEmpresa' => 10, 'cliente' => 'acme']);
$branches = $sdk->branches()->all(['idEmpresa' => 10]);
$points = $sdk->emissionPoints()->byBranch(1, ['idEmpresa' => 10]);
$variants = $sdk->products()->variants(['idEmpresa' => 10, 'start' => 0, 'length' => 20]);
$productsSearch = $sdk->products()->search(['idEmpresa' => 10, 'producto' => 'plan']);
```

Usalo para resolver IDs y abreviaciones antes de crear o editar contratos.
