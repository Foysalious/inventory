<?php


namespace App\Repositories;


use App\Interfaces\CollectionProductsRepositoryInterface;
use App\Models\CollectionProduct;

class CollectionProductsRepository extends BaseRepository implements CollectionProductsRepositoryInterface
{
    public function __construct(CollectionProduct $model)
    {
        parent::__construct($model);
    }
}
