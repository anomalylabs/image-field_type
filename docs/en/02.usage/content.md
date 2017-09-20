## Usage[](#usage)

This section will show you how to use the field type via API and in the view layer.


### Basic Output[](#usage/basic-output)

The image field type always returns `null` or an `\Anomaly\ImageFieldType\Image\Contract\ImageInterface` instance which extends `\Anomaly\FilesModule\File\Contract\FileInterface`.

###### Example

    $entry->example->getName(); // example.jpg

###### Twig

    {{ entry.example.getName() }} // example.jpg


### Presenter Output[](#usage/presenter-output)

When accessing the field value from a decorated entry model the an instance of `\Anomaly\ImageFieldType\Image\ImagePresenter` which extends `\Anomaly\FilesModule\File\FilePresenter` will be returned.

###### Example

    $decorated->example->path; // local://folder/file.ext

    $decorated->example->make()->url() }} // /app/{application}/example/image.jpg

###### Twig

    {{ decorated.example.path }} // local://folder/file.ext

    {{ decorated.example.make.url }} // /app/{application}/example/image.jpg


#### ImagePresenter::cropped()[](#usage/presenter-output/imagepresenter-cropped)

The `cropped` method returns an `Image` instance with the crop data applied.

###### Returns: `\Anomaly\Streams\Platform\Image\Image`

###### Example

    $decorated->example->cropped();

    $decorated->example->cropped()->path();

    $decorated->example->cropped()->widen(100)->path();

###### Twig

    {{ decorated.example.cropped() }}

    {{ decorated.example.cropped().path() }}

    {{ decorated.example.cropped().widen(100).path() }}
