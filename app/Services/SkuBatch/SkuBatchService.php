<?php


namespace App\Services\SkuBatch;


use App\Models\Product;
use App\Models\Sku;
use App\Repositories\SkuBatchRepository;

class SkuBatchService
{
    protected Sku $sku;

    public function __construct(
        protected SkuBatchRepository $skuBatchRepository
    )
    {
    }

    /**
     * @param Product $product
     * @return $this
     */
    public function setSku(Sku $sku)
    {
        $this->sku = $sku;
        return $this;
    }

    public function getLastBatchOfTheSku()
    {
        
    }
}
