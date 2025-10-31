<?php

return [
    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\Models\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook' => [
            'secret' => env('STRIPE_WEBHOOK_SECRET'),
            'tolerance' => env('STRIPE_WEBHOOK_TOLERANCE', 300),
        ],
    ],

    'pusher' => [
        'key' => env('PUSHER_APP_KEY'),
        'secret' => env('PUSHER_APP_SECRET'),
        'app_id' => env('PUSHER_APP_ID'),
        'options' => [
            'host' => env('PUSHER_HOST'),
            'port' => env('PUSHER_PORT', 443),
            'scheme' => env('PUSHER_SCHEME', 'https'),
            'cluster' => env('PUSHER_APP_CLUSTER'),
        ],
    ],

    // UAE Government Services
    'emirates_id' => [
        'base_url' => env('EMIRATES_ID_API_URL', 'https://api.emiratesid.ae'),
        'api_key' => env('EMIRATES_ID_API_KEY'),
        'timeout' => env('EMIRATES_ID_TIMEOUT', 30),
    ],

    'moi' => [
        'base_url' => env('MOI_API_URL', 'https://api.moi.ae'),
        'api_key' => env('MOI_API_KEY'),
        'timeout' => env('MOI_TIMEOUT', 30),
    ],

    'mohre' => [
        'base_url' => env('MOHRE_API_URL', 'https://api.mohre.ae'),
        'api_key' => env('MOHRE_API_KEY'),
        'timeout' => env('MOHRE_TIMEOUT', 30),
    ],

    'visa' => [
        'base_url' => env('VISA_API_URL', 'https://api.visa.ae'),
        'api_key' => env('VISA_API_KEY'),
        'timeout' => env('VISA_TIMEOUT', 30),
    ],
];
