# Puntos De Emision

```php
$points = $sdk->emissionPoints()->all(['idEmpresa' => 10]);
$point = $sdk->emissionPoints()->getById(1);
$pointsByBranch = $sdk->emissionPoints()->byBranch(1, ['idEmpresa' => 10]);
```
