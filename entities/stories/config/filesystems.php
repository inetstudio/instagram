<?php

return [

    /*
     * Расширение файла конфигурации app/config/filesystems.php
     * добавляет локальный диск для хранения медиа историй
     */

    'instagram_stories' => [
        'driver' => 'local',
        'root' => storage_path('app/public/instagram/stories'),
        'url' => env('APP_URL').'/storage/instagram/stories',
        'visibility' => 'public',
    ],
];
