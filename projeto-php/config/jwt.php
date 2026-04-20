<?php

return [
    'ttl' => (int) env('JWT_TTL', 3600),
    'issuer' => env('JWT_ISSUER', env('APP_URL', 'http://localhost')),
    'cookie_name' => env('JWT_COOKIE_NAME', 'catalogo_token'),
    'cookie_secure' => (bool) env('JWT_COOKIE_SECURE', false),
    'cookie_same_site' => env('JWT_COOKIE_SAME_SITE', 'strict'),
];
