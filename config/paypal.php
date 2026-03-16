<?php

return [
    'live' => env('PAYPAL_LIVE', false),
    'api_username' => env('PAYPAL_API_USERNAME'),
    'api_password' => env('PAYPAL_API_PASSWORD'),
    'client_id' => env('PAYPAL_CLIENT_ID'),
    'client_secret' => env('PAYPAL_CLIENT_SECRET'),
    'webhook_id' => env('PAYPAL_WEBHOOK_ID'),
];
