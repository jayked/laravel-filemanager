<?php namespace Jayked\Laravelfilemanager\controllers;

use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Input;
use Jayked\Laravelfilemanager\Events\ImageWasDeleted;

/**
 * Class CropController
 * @package Jayked\Laravelfilemanager\controllers
 */
class DeleteController extends LfmController {
	public $option = 'remove';

    /**
     * Delete image and associated thumbnail
     *
     * @return mixed
     */
    public function getDelete()
    {
        $name_to_delete = Input::get('items');

        $file_path = parent::getPath('directory');

        $file_to_delete = $file_path . $name_to_delete;
        $thumb_to_delete = parent::getPath('thumb') . $name_to_delete;

        if (!$this->storage->exists($file_to_delete)) {
            return $file_to_delete . ' not found!';
        }

        if (is_dir($this->storage->path($file_to_delete))) {
            if (sizeof($this->storage->files($file_to_delete)) != 0) {
                return Lang::get('laravel-filemanager::lfm.error-delete');
            }

            $this->storage->deleteDirectory($file_to_delete);

            return 'OK';
        }

        $this->storage->delete($file_to_delete);
        event(new ImageWasDeleted($file_to_delete));
        
        if ('Images' === $this->file_type) {
            $this->storage->delete($thumb_to_delete);
        }

        return 'OK';
    }

}
