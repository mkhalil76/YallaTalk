<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN') ?: 'sandbox4e3049fc53554d0fb55a8e25f5a077e8.mailgun.org',
        'secret' => env('MAILGUN_SECRET') ?: '',
        'api_key' => env('MAILGUN_API_KEY') ?: 'key-5846b3bc50c4541c1640594bc8e5d8b6'
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => YallaTalk\User::class,
        'key' => env('STRIPE_KEY') ?: 'pk_test_cScLea59bVzfwND2NxaMDIfK',
        'secret' => env('STRIPE_SECRET') ?: 'sk_test_GUIQAg3SxlNsixHF74g5OSNr',
        'description' => env('STRIPE_SERVICE_DESCRIPTION') ?: 'price of service'
    ],

    'facebook' => [
        'client_id' => '138948146825534',
        'client_secret' => 'a704da8297d300c0feb0acc4a5eeb452',
        'redirect' => env('APP_URL', 'http://localhost') . '/api/login/facebook/callback',
    ],

    'FCM' => [
        'FCM_SERVER_KEY' => env('FCM_SERVER_KEY') ?: 'AAAA-svY7cw:APA91bHQVtgktrnlauhsY7rH_BwGIghXefqgWWQyqnzucFTBYWYICXAt7m7h8EHH2m1AIso5P3kV7SqQG0DjVHS9Xwt-37768SAmcwqBfFd-b1TQAoPnyJ5QZAHP74_NHwPJOnNuhGl6',
        
        'FCM_SENDER_ID' => env('FCM_SENDER_ID') ?: '1077161815500'
    ],
    'dropbox' => [
        'API_KEY' => env('DROPBOX_API_KEY') ?: 'nnix9o0bnbqrkbx',
        'API_SECRET' => env('DROPBOX_API_SECRET') ?: 'fnzu8iexscm7qcs',
        'API_TOKEN' => env('DROPBOX_TOKEN') ?: 'zcOzl6LNMkAAAAAAAAAAD8CyxvGMyzPcljSKOu9YaLEn_QX20QjbyVdsyYw_y8KC'
    ]

];
