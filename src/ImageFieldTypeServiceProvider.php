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
        ],
        'admin/image-field_type/choose/{key}'           => [
            'verb' => 'get',
            'uses' => 'Anomaly\ImageFieldType\Http\Controller\\FilesController@choose',
        ],
        'admin/image-field_type/selected/{key}'         => [
            'verb' => 'get',
            'uses' => 'Anomaly\ImageFieldType\Http\Controller\\FilesController@selected',
        ],
        'admin/image-field_type/view/{id}/{key}'        => [
            'verb' => 'get',
            'uses' => 'Anomaly\ImageFieldType\Http\Controller\\FilesController@view',
        ],
        'admin/image-field_type/exists/{folder}/{key}'  => [
            'verb' => 'post',
            'uses' => 'Anomaly\ImageFieldType\Http\Controller\\FilesController@exists',
        ],
        'admin/image-field_type/upload/{folder}/{key}'  => [
            'verb' => 'get',
            'uses' => 'Anomaly\ImageFieldType\Http\Controller\\UploadController@index',
        ],
        'admin/image-field_type/handle/{key}'           => [
            'verb' => 'post',
            'uses' => 'Anomaly\ImageFieldType\Http\Controller\\UploadController@upload',
        ],
        'admin/image-field_type/recent/{key}'           => [
            'verb' => 'get',
            'uses' => 'Anomaly\ImageFieldType\Http\Controller\\UploadController@recent',
        ],
    ];

}
