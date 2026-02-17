# Quickstart

```php
<?php

use Bluetech\Sdk\Client;
use Bluetech\Sdk\Config;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\HttpFactory;

$config = new Config('https://ambiente.bluetechsoftware.cloud/cfe', 'TU_TOKEN_JWT');

$httpClient = new GuzzleClient();
$httpFactory = new HttpFactory();

$sdk = new Client($config, $httpClient, $httpFactory, $httpFactory);

$state = $sdk->subscriptions()->contractState(123);
var_dump($state->status);
```
