<?php namespace Anomaly\ImageFieldType;

use stdClass;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Anomaly\Streams\Platform\Ui\Form\FormBuilder;
use Anomaly\ImageFieldType\Table\ValueTableBuilder;
use Anomaly\FilesModule\File\Contract\FileInterface;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Anomaly\Streams\Platform\Addon\FieldType\FieldType;
use Anomaly\ImageFieldType\Image\Contract\ImageInterface;

/**
 * Class ImageFieldType
 *
 * @link   http://pyrocms.com/
 * @author PyroCMS, Inc. <support@pyrocms.com>
 * @author Ryan Thompson <ryan@pyrocms.com>
 */
class ImageFieldType extends FieldType
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
        'aspect_ratio' => null,
        'mode'         => 'default',
    ];

    /**
     * Get the relation.
     *
     * @return BelongsTo
     */
    public function getRelation()
    {
        $entry = $this->getEntry();

        return $entry->belongsTo(
            Arr::get($this->config, 'related', 'Anomaly\ImageFieldType\Image\ImageModel'),
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

        if (!$max = Arr::get($config, 'max')) {
            $max = $server;
        }

        if ($max > $server) {
            $max = $server;
        }

        Arr::set($config, 'max', $max);

        Arr::set($config, 'folders', (array)$this->config('folders', []));

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
        return eval('return ' . strip_tags(str_replace([':', 'x'], '/', $this->config('aspect_ratio'))) . ';');
    }

    /**
     * Return the config key.
     *
     * @return string
     */
    public function configKey()
    {
        Cache::remember($this->getInputName() . '-config', 60 * 60 * 24, function () {
            return $this->getConfig();
        });

        return $this->getInputName() . '-config';

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
        
        if(is_array($file)) {
            $file = $file['id'];
        }

        return $table->setUploaded([$file])->build()->load()->getTableContent();
    }

    /**
     * Return the crop data.
     *
     * @return null|stdClass
     */
    public function data()
    {
        if (!$this->entry) {
            return null;
        }

        return json_decode($this->entry->{$this->getField() . '_data'});
    }

    /**
     * Append the crop data to the model.
     *
     * @param $value
     * @return \Anomaly\Streams\Platform\Support\Presenter|null
     */
    public function decorate($value)
    {
        if (!$value instanceof ImageInterface) {
            return null;
        }

        /* @var ImageModel $value */
        $value->setData(json_decode($this->entry->{$this->getField() . '_data'}));

        return parent::decorate($value);
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
