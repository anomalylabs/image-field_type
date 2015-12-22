<?php namespace Anomaly\ImageFieldType;

use Anomaly\FilesModule\File\Contract\FileInterface;
use Anomaly\ImageFieldType\Image\ImageModel;
use Anomaly\ImageFieldType\Table\ValueTableBuilder;
use Anomaly\Streams\Platform\Addon\FieldType\FieldType;
use Anomaly\Streams\Platform\Ui\Form\FormBuilder;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Robbo\Presenter\Decorator;

/**
 * Class ImageFieldType
 *
 * @link          http://anomaly.is/streams-platform
 * @author        AnomalyLabs, Inc. <hello@anomaly.is>
 * @author        Ryan Thompson <ryan@anomaly.is>
 * @package       Anomaly\ImageFieldType
 */
class ImageFieldType extends FieldType implements SelfHandling
{

    /**
     * The database column type.
     *
     * @var string
     */
    protected $columnType = 'integer';

    /**
     * The input view.
     *
     * @var string
     */
    protected $inputView = 'anomaly.field_type.image::input';

    /**
     * The field type config.
     *
     * @var array
     */
    protected $config = [
        'folders'      => [],
        'min_height'   => 400,
        'aspect_ratio' => null
    ];

    /**
     * The cache repository.
     *
     * @var Repository
     */
    protected $cache;

    /**
     * Create a new FileFieldType instance.
     *
     * @param Repository $cache
     */
    public function __construct(Repository $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Get the relation.
     *
     * @return BelongsTo
     */
    public function getRelation()
    {
        $entry = $this->getEntry();

        return $entry->belongsTo(
            array_get($this->config, 'related', 'Anomaly\ImageFieldType\Image\ImageModel'),
            $this->getColumnName()
        );
    }

    /**
     * Get the config.
     *
     * @return array
     */
    public function getConfig()
    {
        $config = parent::getConfig();

        $post = str_replace('M', '', ini_get('post_max_size'));
        $file = str_replace('M', '', ini_get('upload_max_filesize'));

        $server = $file > $post ? $post : $file;

        if (!$max = array_get($config, 'max')) {
            $max = $server;
        }

        if ($max > $server) {
            $max = $server;
        }

        array_set($config, 'max', $max);

        array_set($config, 'folders', (array)$this->config('folders', []));

        return $config;
    }

    /**
     * Get the database column name.
     *
     * @return null|string
     */
    public function getColumnName()
    {
        return parent::getColumnName() . '_id';
    }

    /**
     * Get the aspect ratio.
     *
     * @return mixed
     */
    public function aspectRatio()
    {
        return eval('return ' . strip_tags(str_replace(':', '/', $this->config('aspect_ratio'))) . ';');
    }

    /**
     * Return the config key.
     *
     * @return string
     */
    public function configKey()
    {
        $key = md5(json_encode($this->getConfig()));

        $this->cache->put('image-field_type::' . $key, $this->getConfig(), 30);

        return $key;
    }

    /**
     * Value table.
     *
     * @return string
     */
    public function valueTable()
    {
        $table = app(ValueTableBuilder::class);

        $file = $this->getValue();

        if ($file instanceof FileInterface) {
            $file = $file->getId();
        }

        return $table->setUploaded([$file])->build()->response()->getTableContent();
    }

    /**
     * Append the crop data to the model.
     *
     * @param Decorator $decorator
     * @param           $value
     * @return \Anomaly\Streams\Platform\Support\Presenter
     */
    public function decorate(Decorator $decorator, $value)
    {
        /* @var ImageModel $value */
        $value->setData(json_decode($this->entry->{$this->getField() . '_data'}));

        return parent::decorate($decorator, $value);
    }

    /**
     * Handle saving the form data ourselves.
     *
     * @param FormBuilder $builder
     */
    public function handle(FormBuilder $builder)
    {
        $entry = $builder->getFormEntry();
        $id    = $builder->getPostValue($this->getField() . '.id');
        $data  = $builder->getPostValue($this->getField() . '.data');

        // See the accessor for how IDs are handled.
        $entry->{$this->getField()} = $data;
        $entry->{$this->getField()} = $id;

        $entry->save();
    }
}
