<?php

return [
    'ttl' => (int) env('JWT_TTL', 3600),
    'issuer' => env('JWT_ISSUER', env('APP_URL', 'http://localhost')),
];
