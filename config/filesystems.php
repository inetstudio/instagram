<?php

return [

    'instagram_posts' => [
        'driver' => 'local',
        'root' => storage_path('app/public/instagram/posts/'),
        'url' => env('APP_URL').'/storage/instagram/posts/',
        'visibility' => 'public',
    ],

    'instagram_users' => [
        'driver' => 'local',
        'root' => storage_path('app/public/instagram/users/'),
        'url' => env('APP_URL').'/storage/instagram/users/',
        'visibility' => 'public',
    ],

];
