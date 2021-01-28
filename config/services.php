<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

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

    'facebook' => [
        'client_id' => '2791728311114924',
        'client_secret' => '361254ec90288a3675227b640b0874ce',
        'redirect' => 'http://nuveto-chat.herokuapp.com/auth/facebook/callback',
        'page-token' => env('FACEBOOK_PAGE_TOKEN', 'EAAnrDZBZALHKwBAIj1eRo4LztVZC6FrOWywXRxOr6AA4dhyo8gIcS9uNMML9gOBUToJePJZAO64zLZAU41O2cRnm3Nu0Gc7JPJZAFzNPlP5gdZBWc5TYk19X1pZAXGJ4UjUquoDHhj7wpZCRxzYeneKFkuP3ZBt7aY6PI66jLZBp6IEz5FIT0JTioec'),
        'app-secret' => env('FACEBOOK_APP_SECRET', '361254ec90288a3675227b640b0874ce'),
    ],

];
