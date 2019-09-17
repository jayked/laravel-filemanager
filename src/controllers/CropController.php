<?php namespace Jayked\Laravelfilemanager\controllers;

use Request;
use View;
use Intervention\Image\Facades\Image;

/**
 * Class CropController
 *
 * @package Jayked\Laravelfilemanager\controllers
 */
class CropController extends LfmController
{
    public $option = 'crop';

    /**
     * Show crop page
     *
     * @return mixed
     */
    public function getCrop()
    {
        $working_dir = Request::get('working_dir');
        $img = parent::getUrl('directory') . Request::get('img');

        return View::make('laravel-filemanager::crop')->with(compact('working_dir', 'img'));
    }

    /**
     * Crop the image (called via ajax)
     */
    public function getCropimage()
    {
        $image = Request::get('img');
        $dataX = Request::get('dataX');
        $dataY = Request::get('dataY');
        $dataHeight = Request::get('dataHeight');
        $dataWidth = Request::get('dataWidth');

        $image = parent::getTruePath($image);

        // crop image
        $tmp_img = Image::make(base_path($image));
        $tmp_img->crop($dataWidth, $dataHeight, $dataX, $dataY)->save(base_path($image));

        // make new thumbnail
        $thumb_img = Image::make(base_path($image));
        $thumb_img->fit(200, 200)->save(parent::getPath('thumb') . parent::getFileName($image)['short']);
    }
}
