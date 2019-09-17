<?php namespace Jayked\Laravelfilemanager\controllers;

use Request;
use Config;
use Storage;
use Jayked\Laravelfilemanager\Exceptions\NotAllowedException;

/**
 * Class LfmController
 *
 * @package Jayked\Laravelfilemanager\controllers
 */
class LfmController extends Controller
{
    /**
     * @var
     */
    public $file_location = null;
    public $dir_location = null;
    public $file_type = null;
    public $option = null;
    /** @var \Illuminate\Contracts\Filesystem\Filesystem|\Illuminate\Filesystem\FilesystemAdapter */
    protected $storage;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->storage = Storage::disk(Config::get('lfm.storage_driver'));

        $this->file_type = Request::get('type', 'Images'); // default set to Images.

        if('Images' === $this->file_type) {
            $this->dir_location = Config::get('lfm.images_url');
            $this->file_location = Config::get('lfm.images_dir');
        } elseif('Files' === $this->file_type) {
            $this->dir_location = Config::get('lfm.files_url');
            $this->file_location = Config::get('lfm.files_dir');
        } else {
            throw new \Exception('unexpected type parameter');
        }

        $this->dir_location .= '/';
        $this->file_location .= '/';

        $this->checkDefaultFolderExists('user');
        $this->checkDefaultFolderExists('share');

        // Check for the option if it is not null
        if(!is_null($this->option)) {
            $this->checkOption($this->option);
        }
    }

    /**
     * Show the filemanager
     *
     * @return mixed
     */
    public function show()
    {
        $working_dir = '/';
        $working_dir .= (Config::get('lfm.allow_multi_user')) ? $this->getUserSlug()
            : Config::get('lfm.shared_folder_name');
        $working_tree = explode('/', $working_dir);
        $show_list = 1;
        if(Config::get('lfm.view') == 'thumbnails') {
            $show_list = 0;
        }
        $options = Config::get('lfm.options');

        return view('laravel-filemanager::index')
            ->with('working_dir', $working_dir)
            ->with('show_list', $show_list)
            ->with('file_type', $this->file_type)
            ->with('working_tree', $working_tree)
            ->with('options', $options);
    }

    /*****************************
     ***   Private Functions   ***
     *****************************/

    private function checkDefaultFolderExists($type = 'share')
    {
        if($type === 'user' && Config::get('lfm.allow_multi_user') !== true) {
            return;
        }

        $path = $this->getPath($type);

        if(!$this->storage->exists($path)) {
            $this->storage->makeDirectory($path, $mode = 0777, true, true);
        }
    }

    private function formatLocation($location, $type = null, $get_thumb = false)
    {
        if($type === 'share') {
            return $location . Config::get('lfm.shared_folder_name');
        } elseif($type === 'user') {
            return $location . $this->getUserSlug();
        }

        $working_dir = Request::get('working_dir');

        // remove first slash
        if(substr($working_dir, 0, 1) === '/') {
            $working_dir = substr($working_dir, 1);
        }

        $location .= $working_dir;

        if($type === 'directory' || $type === 'thumb') {
            $location .= '/';
        }

        //if user is inside thumbs folder there is no need
        // to add thumbs substring to the end of $location
        $in_thumb_folder = preg_match('/' . Config::get('lfm.thumb_folder_name') . '$/i', $working_dir);

        if($type === 'thumb' && !$in_thumb_folder) {
            $location .= Config::get('lfm.thumb_folder_name') . '/';
        }

        return $location;
    }

    /****************************
     ***   Shared Functions   ***
     ****************************/

    public function getUserSlug()
    {
        return empty(auth()->user()) ? '' : \Auth::user()->user_field;
    }

    public function getPath($type = null, $get_thumb = false)
    {
        $path = $this->file_location;

        $path = $this->formatLocation($path, $type);

        return $path;
    }

    public function getUrl($type = null)
    {
        $url = $this->dir_location;

        $url = $this->formatLocation($url, $type);

        $url = str_replace('\\', '/', $url);

        return $url;
    }

    public function getDirectories($path)
    {
        $thumb_folder_name = Config::get('lfm.thumb_folder_name');
        $all_directories = $this->storage->directories($path);

        $arr_dir = [];

        foreach($all_directories as $directory) {
            $dir_name = $this->getFileName($directory);

            if($dir_name['short'] !== $thumb_folder_name) {
                $arr_dir[] = $dir_name;
            }
        }

        return $arr_dir;
    }

    public function getFileName($file)
    {
        $lfm_dir_start = strpos($file, $this->file_location);
        $working_dir_start = $lfm_dir_start + strlen($this->file_location);
        $lfm_file_path = substr($file, $working_dir_start);

        $arr_dir = explode($this->getPathSeperator(), $lfm_file_path);
        $arr_filename['short'] = end($arr_dir);
        $arr_filename['long'] = '/' . $lfm_file_path;
        $arr_filename['base'] = basename($lfm_file_path);

        return $arr_filename;
    }

    public function getTruePath($file)
    {
        $path = str_replace(config('lfm.images_url'), config('lfm.images_dir'), $file);

        return $path;
    }

    protected function getPathSeperator()
    {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? '\\' : '/';
    }

    public function checkOption($option)
    {
        if(!Config::get('lfm.options.' . $option)) {
            throw new NotAllowedException('You are not allowed to use the [' . $option . '] option');
        }
    }
}
