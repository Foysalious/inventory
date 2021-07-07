<?php


namespace App\Services\SkuBatch;


use App\Models\Product;
use App\Repositories\SkuBatchRepository;

class SkuBatchService
{
    protected Product $product;

    public function __construct(
        protected SkuBatchRepository $skuBatchRepository
    )
    {
    }

    /**
     * @param Product $product
     * @return $this
     */
    public function setProduct(Product $product)
    {
        $this->product = $product;
        return $this;
    }

    public function getLastBatchOfTheProduct()
    {
        return $this->skuBatchRepository
            ->whereIn('sku_id', $this->product->skus()->pluck('id'))
            ->orderByDesc('created_at')
            ->first();
    }
}
