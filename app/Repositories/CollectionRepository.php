<?php

namespace App\Repositories;

use App\Interfaces\CollectionRepositoryInterface;
use App\Models\Collection;
use Illuminate\Support\Facades\DB;

class CollectionRepository extends BaseRepository implements CollectionRepositoryInterface
{
    protected string $collectionProductsTable = 'collection_products';

    public function __construct(Collection $model)
    {
        parent::__construct($model);
    }

    public function getAllCollection($offset, $limit, $partner_id)
    {
        return $this->model->where('partner_id', $partner_id)->offset($offset)->limit($limit)->latest()->get();
    }

    public function getDeletionFileNameCollectionImageFromCDN($partner_id, $collection_id, $column_name)
    {
        return $this->model->where('partner_id', $partner_id)->where('id', $collection_id)->first()[$column_name] ?? [];
    }

    public function getLatestCollectionId($partner_id)
    {
        return $this->model->where('partner_id', $partner_id)->latest()->first()->id;
    }

    public function insertCollectionProducts($products, $collection_id)
    {
        for ($i = 0; $i < count($products); $i++)
        {
            DB::table($this->collectionProductsTable)->insertOrIgnore([
                ['collection_id' => $collection_id, 'product_id' => $products[$i]->id ]
            ]);
        }
        return true;
    }

    public function updateCollectionProducts($products, $collection_id)
    {
        DB::table($this->collectionProductsTable)->where('collection_id', $collection_id)->delete();
        return $this->insertCollectionProducts($products, $collection_id);
    }
}
