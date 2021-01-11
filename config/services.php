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
        'redirect' => 'https://sigmademo.nuvetoapps.com.br/auth/facebook/callback',
        'page-token' => env('FACEBOOK_PAGE_TOKEN', 'EAAMNolX1ZCDUBAFSThAJwEjMVqYZBEZAu0ui0KmZCP6NfaAIXIXCQ3oF0k2hOxQILRNmdZAcYCZCMDv4cH9gGdzBHPeu144MoNI9q1rEcPO0oPPLkX5NwahsKs4dQ3yU3ib51t5YaRZBWxiOE9i3mVtpDyxpXHgot8ysThZBL6qeoRAhJ9h0F4Kz'),
        'app-secret' => env('FACEBOOK_APP_SECRET', '361254ec90288a3675227b640b0874ce'),
    ],

];
