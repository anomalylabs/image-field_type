<?php namespace Anomaly\ImageFieldType;

use Anomaly\Streams\Platform\Addon\FieldType\FieldTypeAccessor;

/**
 * Class ImageFieldTypeAccessor
 *
 * @link          http://anomaly.is/streams-platform
 * @author        AnomalyLabs, Inc. <hello@anomaly.is>
 * @author        Ryan Thompson <ryan@anomaly.is>
 * @package       Anomaly\ImageFieldType
 */
class ImageFieldTypeAccessor extends FieldTypeAccessor
{

    /**
     * Set the value.
     *
     * @param $value
     * @return array
     */
    public function set($value)
    {
        $entry = $this->fieldType->getEntry();

        $attributes = $entry->getAttributes();

        if (is_numeric($value)) {
            $attributes[$this->fieldType->getColumnName()] = $value;
        }

        if (is_object($value) && $data = json_encode($value)) {
            $attributes[$this->fieldType->getField() . '_data'] = $data;
        }

        if (is_null($value)) {
            $attributes[$this->fieldType->getColumnName()]      = $value;
            $attributes[$this->fieldType->getField() . '_data'] = $value;
        }

        $entry->setRawAttributes($attributes);
    }
}
