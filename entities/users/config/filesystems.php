<?php

return [

    /*
     * Расширение файла конфигурации app/config/filesystems.php
     * добавляет локальные диск для хранения медиа пользователей
     */

    'instagram_users' => [
        'driver' => 'local',
        'root' => storage_path('app/public/instagram/users'),
        'url' => env('APP_URL').'/storage/instagram/users',
        'visibility' => 'public',
    ],

];
