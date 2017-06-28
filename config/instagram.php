<?php

return [

    /*
     * Адрес сервиса для обращения к API
     */

    'services' => [
        'url' => '',
    ],

    /*
     * Настройки изображений
     */

    'images' => [
        'quality' => 75,
        'sizes' => [
            'post' => [
                'admin_form' => [
                    'width' => 96,
                    'height' => 96,
                ],
                'admin_index' => [
                    'width' => 320,
                    'height' => 320,
                ],
            ],
        ],
    ],
];
