<?php namespace Jayked\Laravelfilemanager\controllers;

use Request;
use Config;

/**
 * Class ItemsController
 *
 * @package Jayked\Laravelfilemanager\controllers
 */
class ItemsController extends LfmController
{
    /**
     * Get the images to load for a selected folder
     *
     * @return mixed
     */
    public function getItems()
    {
        $type = Request::get('type');
        $view = $this->getView($type);
        $path = parent::getPath();
        $options = Config::get('lfm.options');

        $files = $this->storage->files($path);
        $file_info = $this->getFileInfos($files, $type);
        $directories = parent::getDirectories($path);
        $thumb_url = parent::getUrl('thumb');

        return view($view)->with(compact('files', 'file_info', 'directories', 'thumb_url', 'options'));
    }

    private function getFileInfos($files, $type = 'Images')
    {
        $file_info = [];

        foreach($files as $key => $file) {
            $file_name = parent::getFileName($file)['short'];
            $file_created = $this->storage->lastModified($file);
            $file_size = number_format(($this->storage->size($file) / 1024), 2, ".", "");

            if($file_size > 1024) {
                $file_size = number_format(($file_size / 1024), 2, ".", "") . " Mb";
            } else {
                $file_size = $file_size . " Kb";
            }

            if($type === 'Images') {
                $file_type = $this->storage->mimeType($file);
                $icon = '';
            } else {
                $extension = pathinfo($this->storage->path($file_name))['extension'];

                $icon_array = Config::get('lfm.file_icon_array');
                $type_array = Config::get('lfm.file_type_array');

                if(array_key_exists($extension, $icon_array)) {
                    $icon = $icon_array[$extension];
                    $file_type = $type_array[$extension];
                } else {
                    $icon = "fa-file";
                    $file_type = "File";
                }
            }

            $file_info[$key] = [
                'name'    => $file_name,
                'size'    => $file_size,
                'created' => $file_created,
                'type'    => $file_type,
                'icon'    => $icon,
            ];
        }

        return $file_info;
    }

    private function getView($type = 'Images')
    {
        $view = 'laravel-filemanager::images';

        if($type !== 'Images') {
            $view = 'laravel-filemanager::files-list';
        }

        if(Request::get('show_list') == 1 && $type === 'Images') {
            $view .= '-list';
        }

        return $view;
    }
}
