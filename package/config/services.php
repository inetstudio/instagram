<?php

return [

    /*
     * URL для получения информации по адресу.
     */

    'instagram_api' => [
        'url' => env('INSTAGRAM_API_URL', ''),
        'token' => env('INSTAGRAM_API_TOKEN', ''),
        'username' => env('INSTAGRAM_API_USERNAME', ''),
        'password' => env('INSTAGRAM_API_PASSWORD', ''),
    ],
];
