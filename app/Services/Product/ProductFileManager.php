<?php namespace App\Services\Product;


use App\Services\FileManagers\FileManager;

trait ProductFileManager
{
    use FileManager;

    protected function makeProductImages($file, $name)
    {
        return [$file, $this->uniqueFileName($file, $name)];
    }
}
