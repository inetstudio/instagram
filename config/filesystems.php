<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "s3", "rackspace"
    |
    */

    'disks' => [

        'posts' => [
            'driver' => 'local',
            'root' => storage_path('app/public/instagram/posts/'),
            'url' => env('APP_URL').'/storage/instagram/posts/',
            'visibility' => 'public',
        ],

        'users' => [
            'driver' => 'local',
            'root' => storage_path('app/public/instagram/users/'),
            'url' => env('APP_URL').'/storage/instagram/users/',
            'visibility' => 'public',
        ],

    ],

];
