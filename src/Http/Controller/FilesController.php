<?php namespace Anomaly\ImageFieldType\Http\Controller;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Anomaly\FilesModule\File\FileReader;
use Anomaly\FilesModule\Folder\Command\GetFolder;
use Anomaly\ImageFieldType\Support\AllowedFolders;
use Anomaly\ImageFieldType\Table\FileTableBuilder;
use Anomaly\ImageFieldType\Table\ValueTableBuilder;
use Anomaly\Streams\Platform\Support\Authorizer;
use Anomaly\Streams\Platform\Http\Controller\AdminController;
use Anomaly\FilesModule\File\Contract\FileRepositoryInterface;
use Anomaly\FilesModule\Folder\Contract\FolderRepositoryInterface;

/**
 * Class FilesController
 *
 * @link          http://anomaly.is/streams-platform
 * @author        AnomalyLabs, Inc. <hello@anomaly.is>
 * @author        Ryan Thompson <ryan@anomaly.is>
 * @package       Anomaly\ImageFieldType\Http\Controller
 */
class FilesController extends AdminController
{

    /**
     * Return an index of existing files.
     *
     * The table's folders are set from the same key by
     * FileTableFilters, so resolving it here is what stops
     * an unknown key reaching that handler's open fallback.
     *
     * @param FileTableBuilder $table
     * @param                  $key
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(FileTableBuilder $table, $key)
    {
        $this->config($key);

        return $table->render();
    }

    /**
     * Return a list of folders to choose from.
     *
     * Choosing a folder only leads to the uploader, so it
     * takes the same permission the upload itself does.
     *
     * @param FolderRepositoryInterface $folders
     * @param Authorizer                $authorizer
     * @param Request                   $request
     * @param                           $key
     *
     * @return \Illuminate\Contracts\View\View|mixed
     */
    public function choose(
        FolderRepositoryInterface $folders,
        Authorizer $authorizer,
        Request $request,
        $key
    ) {
        $this->authorizeWrite($authorizer);

        $config = $this->config($key);

        $allowed = [];

        foreach (Arr::get($config, 'folders', []) as $identifier) {

            /* @var FolderInterface $folder */
            if ($folder = dispatch_sync(new GetFolder($identifier))) {
                $allowed[] = $folder;
            }
        }

        if (!$allowed) {
            $allowed = $folders->all();
        }

        return $this->view->make(
            'anomaly.field_type.image::choose',
            [
                'key'     => $key,
                'folders' => $allowed,
            ]
        );
    }

    /**
     * Return a table of selected files.
     *
     * @param ValueTableBuilder $table
     * @param                   $key
     * @return null|string
     */
    public function selected(ValueTableBuilder $table, $key)
    {
        return $table
            ->setAllowedFolders(AllowedFolders::ids($this->config($key)))
            ->setUploaded(explode(',', $this->request->get('uploaded')))
            ->make()
            ->getTableContent();
    }

    /**
     * Return the view of an image.
     *
     * This streams the file's contents, so the file must be
     * one the field is configured to reach.
     *
     * @param FileReader              $output
     * @param FileRepositoryInterface $files
     * @param                         $id
     * @param                         $key
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function view(FileReader $output, FileRepositoryInterface $files, $id, $key)
    {
        $config = $this->config($key);

        if (!$file = $files->find($id)) {
            abort(404);
        }

        if (!$folder = $file->getFolder()) {
            abort(404);
        }

        if (!AllowedFolders::permits($config, $folder->getId())) {
            abort(404);
        }

        return $output->make($file);
    }

    /**
     * Check if a file exists.
     *
     * Only the uploader asks this, so it takes the same
     * permission the upload itself does.
     *
     * @param FileRepositoryInterface $files
     * @param Authorizer              $authorizer
     * @param                         $folder
     * @param                         $key
     * @return \Illuminate\Http\JsonResponse
     */
    public function exists(FileRepositoryInterface $files, Authorizer $authorizer, $folder, $key)
    {
        $this->authorizeWrite($authorizer);

        $config = $this->config($key);

        $success = true;
        $exists  = false;

        /* @var FolderInterface|null $folder */
        $folder = dispatch_sync(new GetFolder($folder));

        if (!$folder || !AllowedFolders::permits($config, $folder->getId())) {
            abort(404);
        }

        if ($file = $files->findByNameAndFolder($this->request->get('file'), $folder)) {
            $exists = true;
        }

        return $this->response->json(compact('success', 'exists'));
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
     * An unresolvable key means the caller was never handed
     * this configuration, so nothing is served for it.
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
