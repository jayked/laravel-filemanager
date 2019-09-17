<?php namespace Jayked\Laravelfilemanager\controllers;

use Lang;
use Request;

/**
 * Class FolderController
 *
 * @package Jayked\Laravelfilemanager\controllers
 */
class FolderController extends LfmController
{
    /**
     * Get list of folders as json to populate treeview
     *
     * @return mixed
     */
    public function getFolders()
    {
        $user_path = !$this->getUserSlug() ? null : parent::getPath('user');
        $lfm_user_path = !$this->getUserSlug() ? null : parent::getFileName($user_path);
        $user_folders = !$this->getUserSlug() ? [] : parent::getDirectories($user_path);

        $share_path = parent::getPath('share');
        $lfm_share_path = parent::getFileName($share_path);
        $shared_folders = parent::getDirectories($share_path);

        return view('laravel-filemanager::tree')
            ->with('user_dir', $lfm_user_path['long'])
            ->with('dirs', $user_folders)
            ->with('share_dir', $lfm_share_path['long'])
            ->with('shares', $shared_folders);
    }

    /**
     * Add a new folder
     *
     * @return mixed
     */
    public function getAddfolder()
    {
        $path = $this->getPath('directory') . '/' . Request::get('name');

        if(!$this->storage->exists($path)) {
            $this->storage->makeDirectory($path, $mode = 0777, true, true);

            return 'OK';
        } else if(empty($folder_name)) {
            return Lang::get('laravel-filemanager::lfm.error-folder-name');
        } else {
            return Lang::get('laravel-filemanager::lfm.error-folder-exist');
        }
    }
}
