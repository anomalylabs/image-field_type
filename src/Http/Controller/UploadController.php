<?php namespace Anomaly\ImageFieldType\Http\Controller;

use Illuminate\Support\Facades\Cache;
use Anomaly\FilesModule\File\FileSanitizer;
use Anomaly\FilesModule\File\FileUploader;
use Anomaly\FilesModule\Folder\Command\GetFolder;
use Anomaly\FilesModule\Folder\Contract\FolderRepositoryInterface;
use Anomaly\ImageFieldType\Support\AllowedFolders;
use Anomaly\ImageFieldType\Table\UploadTableBuilder;
use Anomaly\Streams\Platform\Support\Authorizer;
use Anomaly\Streams\Platform\Http\Controller\AdminController;

/**
 * Class UploadController
 *
 * @link          http://anomaly.is/streams-platform
 * @author        AnomalyLabs, Inc. <hello@anomaly.is>
 * @author        Ryan Thompson <ryan@anomaly.is>
 * @package       Anomaly\ImageFieldType\Http\Controller
 */
class UploadController extends AdminController
{

    /**
     * Return the uploader.
     *
     * @param UploadTableBuilder $table
     * @param Authorizer         $authorizer
     * @param                    $folder
     * @param                    $key
     * @return \Illuminate\View\View
     */
    public function index(UploadTableBuilder $table, Authorizer $authorizer, $folder, $key)
    {
        $this->authorizeWrite($authorizer);

        $config = $this->config($key);

        /* @var FolderInterface $folder */
        $folder = dispatch_sync(new GetFolder($folder));

        if (!$folder || !AllowedFolders::permits($config, $folder->getId())) {
            abort(404);
        }

        return $this->view->make(
            'anomaly.field_type.image::upload/index',
            [
                'folder' => $folder,
                'table'  => $table->setAllowedFolders(AllowedFolders::ids($config))->make()->getTable(),
                'key'    => $key,
            ]
        );
    }

    /**
     * Upload a file.
     *
     * @param FileUploader              $uploader
     * @param FolderRepositoryInterface $folders
     * @param Authorizer                $authorizer
     * @param                           $key
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(
        FileUploader $uploader,
        FolderRepositoryInterface $folders,
        Authorizer $authorizer,
        $key
    ) {
        $this->authorizeWrite($authorizer);

        $config = $this->config($key);

        if (!$file = $this->request->file('upload')) {
            return $this->response->json(['message' => 'No file was uploaded.'], 422);
        }

        if (!$folder = $folders->find($this->request->get('folder'))) {
            return $this->response->json(['message' => 'The folder could not be found.'], 404);
        }

        if (!AllowedFolders::permits($config, $folder->getId())) {
            return $this->response->json(['message' => 'That folder is not allowed for this field.'], 403);
        }

        /*
         * The uploader validates against the folder, which may
         * accept things that are not images. An image field
         * takes images.
         */
        $images = array_map('strtolower', (array)config('anomaly.module.files::mimes.types.image', []));

        if (!in_array($this->extension($file), $images, true)) {
            return $this->response->json(['message' => 'That file is not an image.'], 422);
        }

        try {
            $entry = $uploader->upload($file, $folder);
        } catch (\Exception $e) {
            return $this->response->json(['message' => $e->getMessage()], 422);
        }

        return $this->response->json($entry->getAttributes());
    }

    /**
     * Return the recently uploaded files.
     *
     * @param UploadTableBuilder $table
     * @param Authorizer         $authorizer
     * @param                    $key
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function recent(UploadTableBuilder $table, Authorizer $authorizer, $key)
    {
        $this->authorizeWrite($authorizer);

        return $table
            ->setAllowedFolders(AllowedFolders::ids($this->config($key)))
            ->setUploaded(explode(',', $this->request->get('uploaded')))
            ->make()
            ->getTableContent();
    }


    /**
     * Return the extension the uploader will store the file under.
     *
     * Derived through FileSanitizer so this and FileUploader agree
     * on a name like "x.png.pdf".
     *
     * @param  UploadedFile $file
     * @return string
     */
    protected function extension($file)
    {
        return strtolower(
            pathinfo(FileSanitizer::clean($file->getClientOriginalName()), PATHINFO_EXTENSION)
        );
    }

    /**
     * Refuse a caller who may not write files.
     *
     * @param Authorizer $authorizer
     */
    protected function authorizeWrite(Authorizer $authorizer)
    {
        if (!$authorizer->authorize('anomaly.module.files::files.write')) {
            abort(403);
        }
    }

    /**
     * Return the configuration the key stands for.
     *
     * @param  string $key
     * @return array
     */
    protected function config($key)
    {
        if (!$config = Cache::get($key)) {
            abort(404);
        }

        return (array)$config;
    }
}
