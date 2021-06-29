<?php namespace App\Repositories;

use App\Interfaces\SkuBatchRepositoryInterface;
use App\Models\SkuBatch;

class SkuBatchRepository extends BaseRepository implements SkuBatchRepositoryInterface
{
    public function __construct(SkuBatch $model)
    {
        parent::__construct($model);
    }
}
