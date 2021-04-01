<?php


namespace App\Services\ProductImage;


use App\Interfaces\ProductImageRepositoryInterface;
use App\Services\FileManagers\CdnFileManager;
use App\Services\FileManagers\FileManager;

class Updater
{
    use FileManager, CdnFileManager;

    protected $productImageRepositoryInterface;
    protected $productId;

    public function __construct(ProductImageRepositoryInterface $productImageRepository)
    {
        $this->productImageRepositoryInterface = $productImageRepository;
    }
}
