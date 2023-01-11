<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Credentials
    |--------------------------------------------------------------------------
    |
    | Nordigen credentials.
    | You can get them from https://ob.nordigen.com/user-secrets/
    |
    */
    'credentials' => [
        'secret_id' => env('NORDIGEN_SECRET_ID'),
        'secret_key' => env('NORDIGEN_SECRET_KEY')
    ],

    /*
    |--------------------------------------------------------------------------
    | Redirects
    |--------------------------------------------------------------------------
    |
    | These redirects are used from requisition requesting:
    |   requisition_uri -> Is where the user is redirected after bank has been linked, here we create the requisition on our database
    |   fallback_uri -> If the bank was not linked, and we got an error during this process
    |   success_uri -> Finally we redirect the user here, you can change it with whatever you want
    */
    'redirect' => [
        'requisition_uri' => '/laravel-nordigen/nordigen-redirect',
        'fallback_uri' => '/',
        'success_uri' => config('LARAVEL_NORDIGEN_SUCCESS_URI', '/')
    ]
];
