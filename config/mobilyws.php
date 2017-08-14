<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Account Details
    |--------------------------------------------------------------------------
    |
    | Set your mobile number and Password used to log in to
    | http://mobily.ws
    |
    */
    'mobile' => env('MOBILY_WS_MOBILE'),
    'password' =>  env('MOBILY_WS_PASSWORD'),
    // Name of Sender must be approved by mobily.ws for GCC
    'sender' => env('MOBILY_WS_SENDER'),
    
    /*
    |--------------------------------------------------------------------------
    | Universal Settings Required by Mobily.ws
    |--------------------------------------------------------------------------
    |
    | You do not need to change any of these settings.
    |
    |
    */
    
    // Required by mobily.ws Don't Change.
    'applicationType' => 68,
    // 3 when using UTF-8. Don't Change
    'lang' => '3',
    
    // TODO
//    'domainName' => '',
    
    /*
    |--------------------------------------------------------------------------
    | Define options for the Http request. (Guzzle http client options)
    |--------------------------------------------------------------------------
    |
    | You do not need to change any of these settings.
    |
    |
    */
    'guzzle' => [
        'client' => [
            // The Base Uri of the Api. Don't Change this Value.
            'base_uri' => 'http://mobily.ws/api/',
        ],
        
        // Request Options. http://docs.guzzlephp.org/en/stable/request-options.html
        'request' => [
            'http_errors' => true,
            // For debugging
            'debug' => false,
        ]
    ],

];