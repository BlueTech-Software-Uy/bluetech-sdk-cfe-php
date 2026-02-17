<?php

namespace Bluetech\Sdk\Models;

class AuthTokens extends Model
{
    public string $token;
    public string $refresh_token;
    public int $expires_at;
}
