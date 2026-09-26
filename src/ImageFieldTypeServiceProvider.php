<?php namespace Anomaly\ImageFieldType;

use Anomaly\Streams\Platform\Addon\AddonServiceProvider;

/**
 * Class ImageFieldTypeServiceProvider
 *
 * @link          http://anomaly.is/streams-platform
 * @author        AnomalyLabs, Inc. <hello@anomaly.is>
 * @author        Ryan Thompson <ryan@anomaly.is>
 * @package       Anomaly\ImageFieldType
 */
class ImageFieldTypeServiceProvider extends AddonServiceProvider
{

    /**
     * The addon routes.
     *
     * @var array
     */
    protected $routes = [
        'admin/image-field_type/index/{key}'            => [
            'verb' => 'get',
            'uses' => 'Anomaly\ImageFieldType\Http\Controller\\FilesController@index',
            'constraints' => ['key' => '[a-f0-9]{64}'],
        ],
        'admin/image-field_type/choose/{key}'           => [
            'verb' => 'get',
            'uses' => 'Anomaly\ImageFieldType\Http\Controller\\FilesController@choose',
            'constraints' => ['key' => '[a-f0-9]{64}'],
        ],
        'admin/image-field_type/selected/{key}'         => [
            'verb' => 'get',
            'uses' => 'Anomaly\ImageFieldType\Http\Controller\\FilesController@selected',
            'constraints' => ['key' => '[a-f0-9]{64}'],
        ],
        'admin/image-field_type/view/{id}/{key}'        => [
            'verb' => 'get',
            'uses' => 'Anomaly\ImageFieldType\Http\Controller\\FilesController@view',
            'constraints' => ['key' => '[a-f0-9]{64}'],
        ],
        'admin/image-field_type/exists/{folder}/{key}'  => [
            'verb' => 'post',
            'uses' => 'Anomaly\ImageFieldType\Http\Controller\\FilesController@exists',
            'constraints' => ['key' => '[a-f0-9]{64}'],
        ],
        'admin/image-field_type/upload/{folder}/{key}'  => [
            'verb' => 'get',
            'uses' => 'Anomaly\ImageFieldType\Http\Controller\\UploadController@index',
            'constraints' => ['key' => '[a-f0-9]{64}'],
        ],
        'admin/image-field_type/handle/{key}'           => [
            'verb' => 'post',
            'uses' => 'Anomaly\ImageFieldType\Http\Controller\\UploadController@upload',
            'constraints' => ['key' => '[a-f0-9]{64}'],
        ],
        'admin/image-field_type/recent/{key}'           => [
            'verb' => 'get',
            'uses' => 'Anomaly\ImageFieldType\Http\Controller\\UploadController@recent',
            'constraints' => ['key' => '[a-f0-9]{64}'],
        ],
    ];

}
