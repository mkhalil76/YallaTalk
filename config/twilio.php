<?php

return [

    'twilio' => [

        'default' => 'twilio',

        'connections' => [

            'twilio' => [

                /*
                |--------------------------------------------------------------------------
                | SID
                |--------------------------------------------------------------------------
                |
                | Your Twilio Account SID #
                |
                */

                'sid' => env('TWILIO_ACCOUNT_SID') ?: 'AC2038d1eacbb52e1cb0e7e9198b28af38',

                /*
                |--------------------------------------------------------------------------
                | Access Token
                |--------------------------------------------------------------------------
                |
                | Access token that can be found in your Twilio dashboard
                |
                */

                'token' => env('TWILIO_TOKEN') ?: '8dfd81ae5ed1f42abd0ec549d73c47ad',

                /*
                |--------------------------------------------------------------------------
                | From Number
                |--------------------------------------------------------------------------
                |
                | The Phone number registered with Twilio that your SMS & Calls will come from
                |
                */

                'from' => env('TWILIO_FROM') ?: '(833) 962-3409',

                /*
                |--------------------------------------------------------------------------
                | Your Twilio VIDEO KEY
                |--------------------------------------------------------------------------
                |
                | TWILIO VIDEO API KEY 
                |
                */

                'key' => env('TWILIO_VIDEO_KEY') ?: 'SK9378be785be4516a34120b44e9259a43',

                /*
                |--------------------------------------------------------------------------
                | Your Twilio VIDEO SECRET
                |--------------------------------------------------------------------------
                |
                | TWILIO VIDEO API SECRET 
                |
                */

                'secret' => env('TWILIO_VIDEO_SECRET') ?: 'nDH7mhw0CtnbjGwI2XPkYQoKTkMFghtX',

                /*
                |--------------------------------------------------------------------------
                | Verify Twilio's SSL Certificates
                |--------------------------------------------------------------------------
                |
                | Allows the client to bypass verifying Twilio's SSL certificates.
                | It is STRONGLY advised to leave this set to true for production environments.
                |
                */

                'ssl_verify' => true,
            ],
        ],
    ],
];
