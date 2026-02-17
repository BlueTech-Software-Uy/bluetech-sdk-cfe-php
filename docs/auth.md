# Autenticacion

El SDK permite autenticarse sin pasos manuales usando usuario y secret.

## Login

```php
$tokens = $sdk->auth()->login('usuario', 'secret');
$config->setToken($tokens->token);
$config->setRefreshToken($tokens->refresh_token);
```

## Refresh token

```php
$tokens = $sdk->auth()->exchangeRefreshToken($tokens->refresh_token);
$config->setToken($tokens->token);
```

## Login + setToken (atajo)

```php
$tokens = $sdk->auth()->loginAndSetToken('usuario', 'secret');
```

## Refresh automatico en 401

Si configuraste `refresh_token`, el SDK intenta refrescar automaticamente el token cuando recibe 401.

## Cache en disco (tokens)

```php
use Bluetech\\Sdk\\Auth\\FileTokenStore;

$store = new FileTokenStore(__DIR__ . '/.tokens.json');
$config->setTokenStore($store);

// Si el archivo existe, Config cargara los tokens automaticamente.
```

## Headers

Luego de autenticarse, el SDK agrega automaticamente:

```
Authorization: Bearer <token>
```
