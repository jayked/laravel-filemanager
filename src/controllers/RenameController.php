<?php namespace Jayked\Laravelfilemanager\controllers;

use Illuminate\Support\Facades\Input;

/**
 * Class RenameController
 * @package Jayked\Laravelfilemanager\controllers
 */
class RenameController extends LfmController {
	public $option = 'rename';

    /**
     * @return string
     */
    public function getRename()
    {
        $old_name = Input::get('file');
        $new_name = Input::get('new_name');

        $file_path  = parent::getPath('directory');
        $thumb_path = parent::getPath('thumb');

        $old_file = $file_path . $old_name;

        if($this->storage->exists($old_file)) {
        $new_file = $file_path . $new_name;
            $this->storage->move($old_file, $new_file);

            if('Images' === $this->file_type && $this->storage->exists($thumb_path . $old_name)) {
                $this->storage->move($thumb_path . $old_name, $thumb_path . $new_name);
        }
        }

        return 'OK';
    }
}
