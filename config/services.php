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
        'redirect' => 'https://3293d7cd6ff9.ngrok.io/auth/facebook/callback',
        'page-token' => env('FACEBOOK_PAGE_TOKEN', 'EAAnrDZBZALHKwBANeSnZAbealn57yTm4v5GwXzv2lEKS57r9qlnXnZB7k9KhVBZCfVb8JSvwAcriuf2XJOF82ZCiAcWKztuOgDa2JmsDXbqmHgH6fDcWlCO4DrRbmIbD332eKmwcUzZA1ZClQlUUk3Ha7Gz11U03HZAWZB0Q1KmZCotegZDZD'),
        'app-secret' => env('FACEBOOK_APP_SECRET', '361254ec90288a3675227b640b0874ce'),
    ],

];
