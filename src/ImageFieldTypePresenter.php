<?php namespace Anomaly\ImageFieldType;

use Anomaly\FilesModule\File\Contract\FileInterface;
use Anomaly\Streams\Platform\Addon\FieldType\FieldTypePresenter;
use Anomaly\Streams\Platform\Image\Image;

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

    /**
     * Return a cropped image resource.
     *
     * @return Image
     */
    public function cropped()
    {
        /* @var FileInterface $file */
        if (!$file = $this->object->getValue()) {
            return null;
        }

        $data  = $this->data();
        $image = $file->image();

        return $image->crop($data->width, $data->height, $data->x, $data->y);
    }

    /**
     * Modify the density of the image.
     *
     * @return Image
     */
    public function density($multiplier = 2)
    {
        /* @var FileInterface $file */
        if (!$file = $this->object->getValue()) {
            return null;
        }

        $data  = $this->data();
        $image = $file->image();

        return $image
            ->resize(
                $file->getWidth() * $multiplier,
                $file->getHeight() * $multiplier
            )
            ->crop(
                $data->width * $multiplier,
                $data->height * $multiplier,
                $data->x * $multiplier,
                $data->y * $multiplier
            );
    }
}
