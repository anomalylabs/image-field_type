<?php

use Anomaly\ImageFieldType\Support\Config\FoldersHandler;

return [
    'folders'      => [
        'type'   => 'anomaly.field_type.checkboxes',
        'config' => [
            'handler' => FoldersHandler::class,
        ],
    ],
    'aspect_ratio' => [
        'type' => 'anomaly.field_type.text',
    ],
    'mode'         => [
        'required' => true,
        'type'     => 'anomaly.field_type.select',
        'config'   => [
            'options' => [
                'default' => 'anomaly.field_type.image::config.mode.option.default',
                'select'  => 'anomaly.field_type.image::config.mode.option.select',
                'upload'  => 'anomaly.field_type.image::config.mode.option.upload',
            ],
        ],
    ],
];
