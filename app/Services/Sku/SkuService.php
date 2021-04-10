<?php namespace App\Services\Sku;


use App\Interfaces\SkuRepositoryInterface;
use App\Services\BaseService;

class SkuService extends BaseService
{
    private $skuRepositoryInterface;

    public function __construct(SkuRepositoryInterface $skuRepositoryInterface)
    {
        $this->skuRepositoryInterface = $skuRepositoryInterface;
    }

    public function getSkusByProductIds($productIds)
    {
        $skus = $this->skuRepositoryInterface->whereIn('product_id', $productIds)
            ->pluck('id', 'product_id');
        return $this->success('Successful', ['skus' => $skus], 200);
    }
}
