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

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
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

    'google' => [
        'client_id'     => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect'      => env('GOOGLE_REDIRECT_URI'),
    ],

    'wso2' => [
        'base_url' => env('WSO2_BASE_URL'),
        'client_id' => env('WSO2_CLIENT_ID'),
        'client_secret' => env('WSO2_CLIENT_SECRET'),
        'redirect_uri' => env('WSO2_REDIRECT_URI'),
        'auth_url' => env('WSO2_AUTH_URL'),
        'token_url' => env('WSO2_TOKEN_URL'),
        'userinfo_url' => env('WSO2_USERINFO_URL'),
        'logout_url' => env('WSO2_LOGOUT_URL'),
        'scim2_users_url' => env('WSO2_SCIM2_USERS_URL'),
        'scim2_roles_url' => env('WSO2_SCIM2_ROLES_URL'),
    ],

];
