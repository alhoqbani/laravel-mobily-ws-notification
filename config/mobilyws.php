<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Account Details
    |--------------------------------------------------------------------------
    |
    | Set the chosen authentication method,
    | You could use your login credentials or the generated apiKey from mobily.ws account
    | possible values: api, password, or auto
    | if you choose auto, we will look for the apiKey key first,
    | if not found, we look for the mobile and password
    |
    */
    
    // Authentication mode
    'authentication' => 'auto',
    
    // Set yor login credentials to communicate with mobily.ws Api
    'mobile' => env('MOBILY_WS_MOBILE'),
    'password' =>  env('MOBILY_WS_PASSWORD'),
    
    // Or use the generated apiKey from your mobily.ws account
    'apiKey' => env('MOBILY_WS_API_KEY'),
    
    // Name of Sender must be approved by mobily.ws
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
        ],
    ],

];
