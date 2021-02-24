<?php namespace App\Repositories;


use App\Interfaces\ProductRepositoryInterface;
use App\Models\Product;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    public function getProductsByPartnerId($partnerId, $offset = 0, $limit = 50)
    {
        return $this->model->where('partner_id', $partnerId)->skip($offset)->take($limit)->get();
    }

}
