<?php


namespace App\Services\Product;


use App\Models\Sku;
use App\Repositories\SkuBatchRepository;
use App\Services\SkuBatch\Creator as SkuBatchCreator;
use App\Services\SkuBatch\SkuBatchDto;
use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

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

    /**
     * @throws UnknownProperties
     */
    public function createBatchStock(Sku $sku, ProductUpdateDetailsObjects $productDetailObject, ?array $accounting_info)
    {
        $this->skuBatchCreator->create(new SkuBatchDto([
            'sku_id' => $sku->id,
            'cost' => $productDetailObject->getCost(),
            'stock' => $productDetailObject->getStock(),
            'from_account' => $accounting_info['from_account'] ?? null,
            'supplier_id' => $accounting_info['supplier_id'] ?? null,
        ]));
    }

    public function updateBatchStock($old_sku, ProductUpdateDetailsObjects $sku)
    {
        if(!$old_sku) {
            return;
        }
        $data = [
            'cost' => $sku->getCost(),
            'stock' => $sku->getStock(),
        ];
        $this->skuBatchRepository->where('sku_id', $old_sku)->orderByDesc('created_at')->first()->update($data);
    }
}
