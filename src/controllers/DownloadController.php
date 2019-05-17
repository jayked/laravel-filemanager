<?php namespace Jayked\Laravelfilemanager\controllers;

use Jayked\Laravelfilemanager\controllers\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;

/**
 * Class DownloadController
 * @package Jayked\Laravelfilemanager\controllers
 */
class DownloadController extends LfmController {
	public $option = 'download';

    /**
     * Download a file
     *
     * @return mixed
     */
    public function getDownload()
    {
        return Response::download($this->getPath('directory') . Input::get('file'));
    }

}
