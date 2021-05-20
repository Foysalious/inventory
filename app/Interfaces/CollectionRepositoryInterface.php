<?php namespace App\Interfaces;


interface CollectionRepositoryInterface extends BaseRepositoryInterface
{
    public function getAllCollection($offset, $limit, $partner_id);
    public function getAllCollectionForWebstore(int $offset, int $limit,int $partner_id);
    public function getDeletionFileNameFromCDN($partner_id, $collection_id, $column_name);
}
