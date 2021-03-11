<?php namespace App\Repositories;


use App\Interfaces\ProductOptionRepositoryInterface;
use App\Models\ProductOption;


class ProductOptionRepository extends BaseRepository implements ProductOptionRepositoryInterface
{

    public function __construct(ProductOption $model)
    {
        parent::__construct($model);
    }
}
