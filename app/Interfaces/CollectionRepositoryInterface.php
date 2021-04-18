<?php


namespace App\Interfaces;


use Illuminate\Http\Request;

interface CollectionRepositoryInterface extends BaseRepositoryInterface
{
    public function getAllCollection($offset, $limit, $partner_id);
    public function getDeletionFileNameCollectionImageFromCDN($partner_id, $collection_id, $column_name);
    public function insertCollectionProducts($products, $collection_id);
    public function updateCollectionProducts($products, $collection_id);
    public function getProductsOfCollection($collection_id);
}
