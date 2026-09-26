<?php namespace Anomaly\ImageFieldType;

use stdClass;
use Anomaly\ImageFieldType\Support\ConfigCache;
use Illuminate\Support\Arr;
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
     * @return null|int|float
     */
    public function aspectRatio()
    {
        $ratio = $this->config('aspect_ratio');

        if (!is_scalar($ratio)) {
            return null;
        }

        $ratio = trim((string)$ratio);

        // Ratios are written as "16:9", "4x3", "16/9" or as a plain number.
        if (preg_match('#^(\d+(?:\.\d+)?)\s*[:x/]\s*(\d+(?:\.\d+)?)$#', $ratio, $matches)) {
            return $matches[2] == 0 ? null : $matches[1] / $matches[2];
        }

        return is_numeric($ratio) ? $ratio + 0 : null;
    }

    /**
     * Return the config key.
     *
     * @return string
     */
    public function configKey()
    {
        return ConfigCache::put($this->getConfig());
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
        $id    = data_get($builder->getPostData(), $this->getInputName() . '.id');
        $data  = data_get($builder->getPostData(), $this->getInputName() . '.data');

        // A frontend race can post an all-zero crop (width/height of 0)
        // while a freshly uploaded image is still loading in the cropper.
        // Treat a degenerate crop as "no usable crop" and fall back to a
        // sensible default rather than persisting the broken value.
        $data = $this->defaultCrop($data, $id);

        // See the accessor for how IDs are handled.
        $entry->{$this->getField()} = $data;
        $entry->{$this->getField()} = $id;

        $entry->save();
    }

    /**
     * Replace a degenerate (zero-size) crop with a sensible default.
     *
     * When no aspect ratio is configured the full image is the correct
     * default, so we store null and let the presenter render it uncropped.
     * When an aspect ratio is configured we compute the largest centered
     * crop matching that ratio (mirroring the cropper's autoCropArea: 1).
     *
     * @param  mixed $data The posted crop data (JSON string, array or object).
     * @param  mixed $id   The posted file id.
     * @return mixed
     */
    protected function defaultCrop($data, $id)
    {
        $decoded = is_string($data) ? json_decode($data) : $data;
        $decoded = is_array($decoded) ? (object)$decoded : $decoded;

        // A valid crop must have a positive width and height.
        if (!is_object($decoded) || (!empty($decoded->width) && !empty($decoded->height))) {
            return $data;
        }

        $ratio = $this->aspectRatio();

        if (!$ratio || !$id) {
            return null;
        }

        /* @var FileInterface $file */
        $file = app('Anomaly\FilesModule\File\Contract\FileRepositoryInterface')->find($id);

        $width  = $file ? $file->getWidth() : null;
        $height = $file ? $file->getHeight() : null;

        if (!$width || !$height) {
            return null;
        }

        // Largest centered box matching the configured aspect ratio.
        if ($width / $height > $ratio) {
            $cropWidth  = (int)round($height * $ratio);
            $cropHeight = $height;
        } else {
            $cropWidth  = $width;
            $cropHeight = (int)round($width / $ratio);
        }

        return json_encode([
            'x'      => (int)round(($width - $cropWidth) / 2),
            'y'      => (int)round(($height - $cropHeight) / 2),
            'width'  => $cropWidth,
            'height' => $cropHeight,
            'rotate' => 0,
        ]);
    }
}
