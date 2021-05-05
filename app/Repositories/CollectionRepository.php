<?php

namespace App\Repositories;

use App\Interfaces\CollectionProductsRepositoryInterface;
use App\Interfaces\CollectionRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use App\Models\Collection;

class CollectionRepository extends BaseRepository implements CollectionRepositoryInterface
{
    protected string $collectionProductsTable = 'collection_products';
    protected ProductRepositoryInterface $productRepositoryInterface;
    protected CollectionProductsRepositoryInterface $collectionProductsRepositoryInterface;

    public function __construct(Collection $model, ProductRepositoryInterface $productRepositoryInterface, CollectionProductsRepositoryInterface $collectionProductsRepositoryInterface)
    {
        $this->productRepositoryInterface = $productRepositoryInterface;
        $this->collectionProductsRepositoryInterface = $collectionProductsRepositoryInterface;
        parent::__construct($model);
    }

    public function getAllCollection($offset, $limit, $partner_id)
    {
        return $this->model->where('partner_id', $partner_id)->offset($offset)->limit($limit)->latest()->get();
    }

    public function getDeletionFileNameFromCDN($partner_id, $collection_id, $column_name) : string
    {
        return $this->model->where('partner_id', $partner_id)->where('id', $collection_id)->first()[$column_name] ?? '';
    }
}
