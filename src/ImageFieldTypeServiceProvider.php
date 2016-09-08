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
     * The addon listeners.
     *
     * @var array
     */
    protected $listeners = [
        'Anomaly\Streams\Platform\Assignment\Event\AssignmentWasCreated' => [
            'Anomaly\ImageFieldType\Listener\AddDataColumn',
        ],
        'Anomaly\Streams\Platform\Assignment\Event\AssignmentWasDeleted' => [
            'Anomaly\ImageFieldType\Listener\DropDataColumn',
        ],
    ];

    /**
     * The singleton bindings.
     *
     * @var array
     */
    protected $singletons = [
        'Anomaly\ImageFieldType\ImageFieldTypeModifier' => 'Anomaly\ImageFieldType\ImageFieldTypeModifier',
    ];

    /**
     * The addon routes.
     *
     * @var array
     */
    protected $routes = [
        'streams/image-field_type/index/{key}'     => 'Anomaly\ImageFieldType\Http\Controller\FilesController@index',
        'streams/image-field_type/choose/{key}'    => 'Anomaly\ImageFieldType\Http\Controller\FilesController@choose',
        'streams/image-field_type/selected'        => 'Anomaly\ImageFieldType\Http\Controller\FilesController@selected',
        'streams/image-field_type/view/{id}'       => 'Anomaly\ImageFieldType\Http\Controller\FilesController@view',
        'streams/image-field_type/upload/{folder}' => 'Anomaly\ImageFieldType\Http\Controller\UploadController@index',
        'streams/image-field_type/handle'          => 'Anomaly\ImageFieldType\Http\Controller\UploadController@upload',
        'streams/image-field_type/recent'          => 'Anomaly\ImageFieldType\Http\Controller\UploadController@recent',
    ];

}
