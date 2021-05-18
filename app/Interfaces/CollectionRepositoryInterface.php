<?php


namespace App\Interfaces;


use Illuminate\Http\Request;

interface CollectionRepositoryInterface extends BaseRepositoryInterface
{
    public function getAllCollection($offset, $limit, $partner_id);
    public function getAllCollectionForWebstore($offset, $limit,$partner_id);
    public function getDeletionFileNameFromCDN($partner_id, $collection_id, $column_name);
}
