<?php namespace App\Repositories;


use App\Interfaces\ProductOptionValueRepositoryInterface;
use App\Models\ProductOptionValue;

class ProductOptionValueRepository extends BaseRepository implements ProductOptionValueRepositoryInterface
{
    public function __construct(ProductOptionValue $model)
    {
        parent::__construct($model);
    }
}
