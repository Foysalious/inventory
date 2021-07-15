<?php namespace App\Services\ProductImage;


use App\Services\FileManagers\ImageManager;

class ProductAppThumb extends ImageManager
{
    public function __construct($file)
    {
        $this->width = 300;
        $this->height = 300;
        $this->file = $file;
    }
}
