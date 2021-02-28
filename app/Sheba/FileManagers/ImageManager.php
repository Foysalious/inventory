<?php


namespace App\Sheba\FileManagers;


use Intervention\Image\Facades\Image;

abstract class ImageManager
{
    protected $file;

    protected $width;

    protected $height;

    public function make()
    {
        if($this->file instanceof \Intervention\Image\Image) return $this->file;

        $image = Image::make($this->file);
        $image->encode($this->file->getClientOriginalExtension());
        return $image;
    }

    public function makeAndResize()
    {
        $image = Image::make($this->file)->resize($this->width, $this->height);
        $image->encode($this->file->getClientOriginalExtension());
        return $image;
    }
}
