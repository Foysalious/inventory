<?php


namespace App\Sheba\FileManagers;

use Intervention\Image\Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;


trait FileManager
{
    protected function makeCollectionThumb($file, $name) : array
    {
        $filename = $this->uniqueFileName($file, $name);
        $file = (new Thumb($file))->make();
        return [$file, $filename];
    }

    protected function uniqueFileName($file, $name, $ext = null) : string
    {
        if(empty($name))
        {
            $name = "TIWNN";
        }

        $name = strtolower(str_replace('', '_', $name));
        return time() . "_" . $name . "." . ($ext ?: $this->getExtension($file));
    }

    private function getExtension($file)
    {
        if ($file instanceof UploadedFile) return $file->getClientOriginalExtension();
        if ($file instanceof Image) return explode('/', $file->mime())[1];
        return getBase64FileExtension($file);
    }
}
