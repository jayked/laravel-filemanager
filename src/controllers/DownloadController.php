<?php namespace Jayked\Laravelfilemanager\controllers;

use Request;

/**
 * Class DownloadController
 *
 * @package Jayked\Laravelfilemanager\controllers
 */
class DownloadController extends LfmController
{
    public $option = 'download';

    /**
     * Download a file
     *
     * @return mixed
     */
    public function getDownload()
    {
        $location = $this->getPath('directory') . Request::get('file');

        return $this->storage->download($location);
    }
}
