<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'oauth' => [
        'meta' => [
            'authorize_url' => env('META_OAUTH_AUTHORIZE_URL'),
            'token_url' => env('META_OAUTH_TOKEN_URL'),
            'client_id' => env('META_OAUTH_CLIENT_ID'),
            'client_secret' => env('META_OAUTH_CLIENT_SECRET'),
            'scopes' => array_filter(explode(',', (string) env('META_OAUTH_SCOPES', 'ads_read,ads_management'))),
        ],
        'google' => [
            'authorize_url' => env('GOOGLE_OAUTH_AUTHORIZE_URL'),
            'token_url' => env('GOOGLE_OAUTH_TOKEN_URL'),
            'client_id' => env('GOOGLE_OAUTH_CLIENT_ID'),
            'client_secret' => env('GOOGLE_OAUTH_CLIENT_SECRET'),
            'scopes' => array_filter(explode(',', (string) env('GOOGLE_OAUTH_SCOPES', 'https://www.googleapis.com/auth/adwords'))),
        ],
        'tiktok' => [
            'authorize_url' => env('TIKTOK_OAUTH_AUTHORIZE_URL'),
            'token_url' => env('TIKTOK_OAUTH_TOKEN_URL'),
            'client_id' => env('TIKTOK_OAUTH_CLIENT_ID'),
            'client_secret' => env('TIKTOK_OAUTH_CLIENT_SECRET'),
            'scopes' => array_filter(explode(',', (string) env('TIKTOK_OAUTH_SCOPES', ''))),
        ],
    ],

];
