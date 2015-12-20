<?php namespace Anomaly\ImageFieldType\Listener;

use Anomaly\ImageFieldType\ImageFieldType;
use Anomaly\Streams\Platform\Assignment\Event\AssignmentWasCreated;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

/**
 * Class AddDataColumn
 *
 * @link          http://anomaly.is/streams-platform
 * @author        AnomalyLabs, Inc. <hello@anomaly.is>
 * @author        Ryan Thompson <ryan@anomaly.is>
 * @package       Anomaly\ImageFieldType\Listener
 */
class AddDataColumn
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
     * @param AssignmentWasCreated $event
     */
    public function handle(AssignmentWasCreated $event)
    {
        $assignment = $event->getAssignment();

        $fieldType = $assignment->getFieldType();

        if (!$fieldType instanceof ImageFieldType) {
            return;
        }

        $table = $assignment->getStreamPrefix() . $assignment->getStreamSlug();

        $this->schema->table(
            $table,
            function (Blueprint $table) use ($assignment) {
                $table->text($assignment->getFieldSlug() . '_data')->nullable(true);
            }
        );
    }
}
