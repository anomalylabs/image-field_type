<?php namespace Anomaly\ImageFieldType;

use Anomaly\Streams\Platform\Addon\FieldType\FieldType;
use Anomaly\Streams\Platform\Addon\FieldType\FieldTypeSchema;
use Anomaly\Streams\Platform\Assignment\Contract\AssignmentInterface;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class ImageFieldTypeSchema
 *
 * @link   http://pyrocms.com/
 * @author PyroCMS, Inc. <support@pyrocms.com>
 * @author Ryan Thompson <ryan@pyrocms.com>
 */
class ImageFieldTypeSchema extends FieldTypeSchema
{

    /**
     * Add the field type columns.
     *
     * @param Blueprint           $table
     * @param AssignmentInterface $assignment
     */
    public function addColumn(Blueprint $table, AssignmentInterface $assignment)
    {
        $nullable = !$assignment->isTranslatable() ? !$assignment->isRequired() : true;

        $table->integer($this->fieldType->getColumnName())->nullable($nullable);
        $table->text($this->fieldType->getField() . '_data')->nullable(true);

        if ($assignment->isUnique() && !$assignment->isTranslatable()) {
            $table->unique(
                $this->fieldType->getColumnName(),
                md5('unique_' . $this->fieldType->getColumnName())
            );
        }
    }

    /**
     * Rename the field type columns.
     *
     * @param Blueprint $table
     * @param FieldType $from
     */
    public function renameColumn(Blueprint $table, FieldType $from)
    {
        $table->renameColumn($from->getColumnName(), $this->fieldType->getColumnName());
        $table->renameColumn($from->getField() . '_data', $this->fieldType->getField() . '_data');
    }

    /**
     * Update an existing column.
     *
     * @param Blueprint           $table
     * @param AssignmentInterface $assignment
     */
    public function updateColumn(Blueprint $table, AssignmentInterface $assignment)
    {
        $nullable = !$assignment->isTranslatable() ? !$assignment->isRequired() : true;

        $table->integer($this->fieldType->getColumnName())->nullable($nullable)->change();
        $table->text($this->fieldType->getField() . '_data')->nullable(true)->change();

        // The unique index name.
        $unique = md5('unique_' . $this->fieldType->getColumnName());

        $hasIndex = $this->schema->hasIndex($table->getTable(), $unique);

        // If unique and not translatable and the index is missing, add it.
        if ($assignment->isUnique() && !$assignment->isTranslatable() && !$hasIndex) {
            $table->unique($this->fieldType->getColumnName(), $unique);
        }

        // If no longer unique and not translatable and the index exists, drop it.
        if (!$assignment->isUnique() && !$assignment->isTranslatable() && $hasIndex) {
            $table->dropIndex($unique);
        }
    }

    /**
     * Drop the field type columns.
     *
     * @param Blueprint $table
     */
    public function dropColumn(Blueprint $table)
    {
        $table->dropColumn($this->fieldType->getColumnName());
        $table->dropColumn($this->fieldType->getField() . '_data');
    }
}
