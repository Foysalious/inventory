<?php namespace App\Repositories;


use App\Interfaces\ProductUpdateLogRepositoryInterface;
use App\Models\ProductUpdateLog;

class ProductUpdateLogRepository extends BaseRepository implements ProductUpdateLogRepositoryInterface
{
    public function __construct(ProductUpdateLog $model)
    {
        parent::__construct($model);
    }
}
