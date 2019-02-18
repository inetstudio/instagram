<?php

return [

    /*
     * Расширение файла конфигурации app/config/filesystems.php
     * добавляет локальный диск для хранения медиа постов
     */

    'instagram_posts' => [
        'driver' => 'local',
        'root' => storage_path('app/public/instagram/posts'),
        'url' => env('APP_URL').'/storage/instagram/posts',
        'visibility' => 'public',
    ],
];
