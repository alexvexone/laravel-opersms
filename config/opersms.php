<?php

return [
    /*
    |--------------------------------------------------------------------------
    | OperSMS
    |--------------------------------------------------------------------------
    |
    | Основные настройки для подключения к OperSMS
    */

    'url' => env('OPERSMS_URL', 'http://83.69.139.182:8080'),

    'login' => env('OPERSMS_LOGIN'),

    'password' => env('OPERSMS_PASSWORD'),

    'ssl_verification' => env('OPERSMS_SSL_VERIFICATION', false),
];
