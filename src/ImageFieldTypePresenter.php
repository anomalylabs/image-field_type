<?php namespace Anomaly\ImageFieldType;

use Anomaly\Streams\Platform\Addon\FieldType\FieldTypePresenter;

/**
 * Class ImageFieldTypePresenter
 *
 * @link          http://anomaly.is/streams-platform
 * @author        AnomalyLabs, Inc. <hello@anomaly.is>
 * @author        Ryan Thompson <ryan@anomaly.is>
 * @package       Anomaly\ImageFieldType
 */
class ImageFieldTypePresenter extends FieldTypePresenter
{

    /**
     * The decorated object.
     *
     * @var ImageFieldType
     */
    protected $object;

    /**
     * Return the image data.
     *
     * @return object|null
     */
    public function data()
    {
        $entry = $this->object->getEntry();

        return json_decode($entry->{$this->object->getField() . '_data'});
    }
}
