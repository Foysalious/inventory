<?php


namespace App\Sheba\FileManagers;


class Thumb extends ImageManager
{
    public function __construct($file)
    {
        $this->width = 300;
        $this->height = 300;
        $this->file = $file;
    }
}
