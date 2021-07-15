<?php namespace App\Services\Product;


use App\Services\FileManagers\FileManager;
use App\Services\ProductImage\ProductAppThumb;

trait ProductFileManager
{
    use FileManager;

    protected function makeProductImages($file, $name): array
    {
        return [$file, $this->uniqueFileName($file, $name)];
    }

    protected function makeProductAppThumb($file, $name): array
    {
        $filename = $this->uniqueFileName($file, $name);
        $file = (new ProductAppThumb($file))->make();
        return [$file, $filename];
    }
}
