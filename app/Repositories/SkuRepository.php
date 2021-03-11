<?php namespace App\Repositories;


use App\Interfaces\SkuRepositoryInterface;
use App\Models\Sku;

class SkuRepository extends BaseRepository implements SkuRepositoryInterface
{
    public function __construct(Sku $model)
    {
        parent::__construct($model);
    }

}
