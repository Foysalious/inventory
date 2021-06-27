<?php namespace App\Services\SkuBatch;

use App\Interfaces\SkuBatchRepositoryInterface;

class Creator
{
    public function __construct(
       protected SkuBatchRepositoryInterface $skuBatchRepository
    )
    {}

    public function create(SkuBatchDto $skuBatchDto)
    {
        dd($skuBatchDto);
        dd('in skuBatch creator');
    }


}
