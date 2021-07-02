<?php


namespace App\Services\Product;


use App\Repositories\SkuBatchRepository;
use App\Services\SkuBatch\Creator as SkuBatchCreator;
use App\Services\SkuBatch\SkuBatchDto;
use Spatie\DataTransferObject\DataTransferObject;

class ProductStockBatchUpdater
{
    public function __construct(
        protected SkuBatchCreator $skuBatchCreator,
        protected SkuBatchRepository $skuBatchRepository
    )
    {
    }

    public function deleteBatchStock($product)
    {
        $skus = $product->skus()->get();
        $skus->each(function ($sku){
            $batches = $sku->batch()->get();
            $batches->each(function ($batch){
                $batch->delete();
            });
        });
    }

    public function createBatchStock($sku, $productDetailObject)
    {
        $this->skuBatchCreator->create(new SkuBatchDto([
            'sku_id' => $sku->id,
            'cost' => $productDetailObject->getChannelData()[0]->getCost(),
            'stock' => $productDetailObject->getStock(),
        ]));
    }

    public function updateBatchStock($old_sku, mixed $stock)
    {
        if(!$old_sku) return;
        $this->skuBatchRepository->where('sku_id',$old_sku)->orderByDesc('created_at')->first()->update(['stock' => $stock]);
    }
}
