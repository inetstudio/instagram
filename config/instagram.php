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
        'posts' => [
            'conversions' => [
                'images' => [
                    'preview' => [
                        [
                            'name' => 'preview_admin_form',
                            'size' => [
                                'width' => 96,
                                'height' => 96,
                            ],
                        ],
                        [
                            'name' => 'preview_admin_index',
                            'size' => [
                                'width' => 320,
                                'height' => 320,
                            ],
                        ],
                        [
                            'name' => 'preview_gallery',
                            'fit' => [
                                'width' => 480,
                                'height' => 480,
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
