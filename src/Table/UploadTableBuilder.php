<?php namespace Anomaly\ImageFieldType\Table;

use Anomaly\FilesModule\File\FileModel;
use Anomaly\Streams\Platform\Ui\Table\TableBuilder;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class UploadTableBuilder
 *
 * @link          http://anomaly.is/streams-platform
 * @author        AnomalyLabs, Inc. <hello@anomaly.is>
 * @author        Ryan Thompson <ryan@anomaly.is>
 */
class UploadTableBuilder extends TableBuilder
{

    /**
     * The uploaded IDs.
     *
     * @var array
     */
    protected $uploaded = [];

    /**
     * The ajax flag.
     *
     * @var bool
     */
    protected $ajax = true;

    /**
     * The table model.
     *
     * @var string
     */
    protected $model = FileModel::class;

    /**
     * The table filters.
     *
     * @var array
     */
    protected $filters = [];

    /**
     * The table columns.
     *
     * @var array
     */
    protected $columns = [
        'entry.preview' => [
            'heading' => 'anomaly.module.files::field.preview.name',
        ],
        'name'          => [
            'sort_column' => 'name',
            'wrapper'     => '
                    <strong>{value.file}</strong>
                    <br>
                    <small class="text-muted">{value.disk}://{value.folder}/{value.file}</small>
                    <br>
                    <span>{value.size} {value.keywords}</span>',
            'value'       => [
                'file'     => 'entry.name',
                'folder'   => 'entry.folder.slug',
                'keywords' => 'entry.keywords.labels|join',
                'disk'     => 'entry.folder.disk.slug',
                'size'     => 'entry.size_label',
            ],
        ],
        'size'          => [
            'sort_column' => 'size',
            'value'       => 'entry.readable_size',
        ],
        'mime_type',
        'folder',
    ];

    /**
     * The table buttons.
     *
     * @var array
     */
    protected $buttons = [
        'select' => [
            'data-file' => 'entry.id',
        ],
    ];

    /**
     * The table options.
     *
     * @var array
     */
    protected $options = [
        'limit'              => 999,
        'container_class'    => '',
        'enable_views'       => false,
        'sortable_headers'   => false,
        'no_results_message' => 'anomaly.field_type.image::message.no_uploads',
    ];

    /**
     * The folder IDs the field permits.
     *
     * @var array
     */
    protected $allowedFolders = [];

    /**
     * Fired just before querying
     * for table entries.
     *
     * @param Builder    $query
     * @param Repository $config
     */
    public function onQuerying(Builder $query, Repository $config)
    {
        $uploaded = $this->getUploaded();

        $query->whereIn('id', $uploaded ?: [0]);

        /*
         * An ID list on its own addresses every file on the
         * install, so it is narrowed to the folders the field
         * names. No folders means the field is unrestricted.
         */
        if ($folders = $this->getAllowedFolders()) {
            $query->whereIn('folder_id', $folders);
        }

        $query->orderBy('updated_at', 'ASC');
        $query->orderBy('created_at', 'ASC');

        $query->whereIn('extension', $config->get('anomaly.module.files::mimes.types.image'));
    }

    /**
     * Get the allowed folder IDs.
     *
     * @return array
     */
    public function getAllowedFolders()
    {
        return $this->allowedFolders;
    }

    /**
     * Set the allowed folder IDs.
     *
     * @param  array $allowedFolders
     * @return $this
     */
    public function setAllowedFolders(array $allowedFolders)
    {
        $this->allowedFolders = $allowedFolders;

        return $this;
    }

    /**
     * Get uploaded IDs.
     *
     * @return array
     */
    public function getUploaded()
    {
        return $this->uploaded;
    }

    /**
     * Set the uploaded IDs.
     *
     * @param  array $uploaded
     * @return $this
     */
    public function setUploaded(array $uploaded)
    {
        $this->uploaded = $uploaded;

        return $this;
    }
}
