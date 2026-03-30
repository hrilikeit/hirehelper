<?php

return [
    'mode' => env('PAYPAL_MODE', 'sandbox'),
    'client_id' => env('PAYPAL_CLIENT_ID'),
    'secret' => env('PAYPAL_SECRET'),
    'brand_name' => env('PAYPAL_BRAND_NAME', env('APP_NAME', 'HireHelper')),
];
