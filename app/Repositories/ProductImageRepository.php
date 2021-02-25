<?php namespace App\Repositories;


use App\Interfaces\ProductImageRepositoryInterface;
use App\Models\ProductImage;

class ProductImageRepository extends BaseRepository implements ProductImageRepositoryInterface
{
    public function __construct(ProductImage $model)
    {
        parent::__construct($model);
    }
}
