# Errores y reintentos

Las respuestas no 2xx se convierten en excepciones:

- `UnauthorizedException` (401)
- `ForbiddenException` (403)
- `NotFoundException` (404)
- `IdempotencyConflictException` (409)
- `ValidationException` (400/422)
- `RateLimitException` (429)
- `ServerException` (5xx)

Configuracion de reintentos:

```php
$config->setRetryMax(3);
$config->setRetryBackoffMs(500);
```
