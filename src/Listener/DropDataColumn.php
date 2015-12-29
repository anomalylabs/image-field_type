<?php namespace Anomaly\ImageFieldType\Listener;

use Anomaly\ImageFieldType\ImageFieldType;
use Anomaly\Streams\Platform\Assignment\Event\AssignmentWasDeleted;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

/**
 * Class DropDataColumn
 *
 * @link          http://anomaly.is/streams-platform
 * @author        AnomalyLabs, Inc. <hello@anomaly.is>
 * @author        Ryan Thompson <ryan@anomaly.is>
 * @package       Anomaly\ImageFieldType\Listener
 */
class DropDataColumn
{

    /**
     * The schema builder.
     *
     * @var Builder
     */
    protected $schema;

    /**
     * Create a new StreamSchema instance.
     */
    public function __construct()
    {
        $this->schema = app('db')->connection()->getSchemaBuilder();
    }

    /**
     * Handle the event.
     *
     * @param AssignmentWasDeleted $event
     */
    public function handle(AssignmentWasDeleted $event)
    {
        $assignment = $event->getAssignment();

        $fieldType = $assignment->getFieldType();

        if (!$fieldType instanceof ImageFieldType) {
            return;
        }

        $table = $assignment->getStreamPrefix() . $assignment->getStreamSlug();

        if (!$this->schema->hasTable($table)) {
            return;
        }

        $this->schema->table(
            $table,
            function (Blueprint $table) use ($assignment) {
                $table->dropColumn($assignment->getFieldSlug() . '_data');
            }
        );
    }
}
