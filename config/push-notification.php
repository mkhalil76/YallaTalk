<?php

//production
return array(

    'YALLATALK_IOS'  => array(
        'environment' =>'development',
        'certificate' => storage_path('cer/pushcertnew.pem'),
        'passPhrase'  => 'password',
        'service'     => 'apns'
    ),
    'YALLATALK_ANDROID' => array(
        'environment' => 'development',
        'apiKey'      => 'yourAPIKey',
        'service'     => 'gcm'
    )
);
