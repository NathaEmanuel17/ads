<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Services\ImageService;

class DetailsController extends BaseController
{
    public function index()
    {
        //
    }

    public function image(string $image = null, string $sizeImage = 'regular')
    {
        ImageService::showImage('adverts', $image, $sizeImage);
        
    }
}
