<?php namespace Anomaly\ImageFieldType\Image;

use Anomaly\FilesModule\File\FileModel;

/**
 * Class ImageModel
 *
 * @link          http://anomaly.is/streams-platform
 * @author        AnomalyLabs, Inc. <hello@anomaly.is>
 * @author        Ryan Thompson <ryan@anomaly.is>
 * @package       Anomaly\ImageFieldType\Image
 */
class ImageModel extends FileModel
{

    /**
     * The crop data.
     *
     * @var \stdClass
     */
    protected $data = [];

    /**
     * Get the data.
     *
     * @return \stdClass
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set the data.
     *
     * @param \stdClass $data
     * @return $this
     */
    public function setData(\stdClass $data)
    {
        $this->data = $data;

        return $this;
    }
}
