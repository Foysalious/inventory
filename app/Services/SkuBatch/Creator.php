<?php namespace App\Services\SkuBatch;

use App\Interfaces\SkuBatchRepositoryInterface;
use App\Traits\ModificationFields;

class Creator
{
    use ModificationFields;
    public function __construct(
       protected SkuBatchRepositoryInterface $skuBatchRepository
    )
    {}

    public function create(SkuBatchDto $skuBatchDto)
    {
        return $this->skuBatchRepository->create(($skuBatchDto->toArray()));
    }


}
