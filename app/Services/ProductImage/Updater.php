<?php


namespace App\Services\ProductImage;


use App\Interfaces\ProductImageRepositoryInterface;
use App\Services\FileManagers\CdnFileManager;
use App\Services\FileManagers\FileManager;
use App\Services\Product;

class Updater
{
    use FileManager, CdnFileManager;

    protected $productImageRepositoryInterface;
    protected $productId, $productUpdater;

    public function __construct(ProductImageRepositoryInterface $productImageRepository, Updater $updater)
    {
        $this->productImageRepositoryInterface = $productImageRepository;
        $this->productUpdater = $updater;
    }
}
