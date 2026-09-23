<?php

return [

    'url' => env('CENTRIFUGO_URL', 'http://localhost:8000'),

    'api_key' => env('CENTRIFUGO_API_KEY'),

    'hmac_secret' => env('CENTRIFUGO_HMAC_SECRET'),

    'token_expire' => env('CENTRIFUGO_TOKEN_EXPIRE', 3600),

];
