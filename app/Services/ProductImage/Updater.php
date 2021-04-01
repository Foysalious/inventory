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

    public function deleteRequestedProductImages($productId, $deleteRequestedImageList)
    {
        for ($i=0; $i < count($deleteRequestedImageList); $i++)
        {
            $imageLink = $this->productImageRepositoryInterface->where('product_id', $productId)
                ->where('id', $deleteRequestedImageList[$i])
                ->first()['image_link'];

            $this->productImageRepositoryInterface->where('product_id', $productId)->where('id', $deleteRequestedImageList[$i])->delete();
            $this->deleteFileFromCDNPath($imageLink);
        }
    }
}
