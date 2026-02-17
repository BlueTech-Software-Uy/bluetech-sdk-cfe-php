# Listas de precios

```php
$priceBook = $sdk->priceBooks()->create([
    'name' => 'SaaS Default',
    'currency' => 'UYU',
    'items' => [
        ['sku' => 'API_CALL', 'price' => 0.02, 'unit' => 'call'],
    ],
], 10);

$versions = $sdk->priceBooks()->versions($priceBook->id);
```
