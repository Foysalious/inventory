<?php namespace App\Services\SkuBatch;

use App\Interfaces\SkuBatchRepositoryInterface;
use App\Models\SkuBatch;
use App\Traits\ModificationFields;
use phpDocumentor\Reflection\Types\This;

class Updater
{
    use ModificationFields;
    public function __construct(
       protected SkuBatchRepositoryInterface $skuBatchRepository
    )
    {}

    protected SkuBatchDto $skuBatchDto;

    /**
     * @param SkuBatchDto $skuBatchDto
     */
    public function setSkuBatchDto(SkuBatchDto $skuBatchDto)
    {
        $this->skuBatchDto = $skuBatchDto;
        return $this;
    }

    public function update()
    {
        $last_batch = $this->getSkuLastBatch();
        $last_batch->cost = $this->skuBatchDto->cost;
        $last_batch->stock = $this->skuBatchDto->stock;
        $last_batch->save();
    }

    private function getSkuLastBatch()
    {
        return $this->skuBatchRepository->where('sku_id', $this->skuBatchDto->sku_id)->orderByDesc('created_at')->first();
    }


}
