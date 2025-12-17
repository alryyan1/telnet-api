<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Morpho API Base URL
    |--------------------------------------------------------------------------
    |
    | The base URL for the Morpho IoT Device API.
    |
    */

    'api_base_url' => env('MORPHO_API_BASE_URL', 'https://morpho.challengeone.tn'),

    /*
    |--------------------------------------------------------------------------
    | Token Cache Duration
    |--------------------------------------------------------------------------
    |
    | The duration in minutes to cache the authentication token.
    | Default is 60 minutes (1 hour).
    |
    */

    'token_cache_duration' => env('MORPHO_TOKEN_CACHE_DURATION', 60),

    /*
    |--------------------------------------------------------------------------
    | JWT Secret Key
    |--------------------------------------------------------------------------
    |
    | The secret key used to sign JWT tokens. If not set, will use APP_KEY.
    | You can generate a secure key using: php artisan key:generate
    |
    */

    'jwt_secret' => env('MORPHO_JWT_SECRET', null),

    /*
    |--------------------------------------------------------------------------
    | JWT Token Expiration
    |--------------------------------------------------------------------------
    |
    | The expiration time in minutes for JWT tokens.
    | Default is 60 minutes (1 hour).
    |
    */

    'jwt_expiration' => env('MORPHO_JWT_EXPIRATION', 60),
];

