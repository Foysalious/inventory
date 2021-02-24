<?php


namespace App\Interfaces;


use Illuminate\Http\Request;

interface CollectionRepositoryInterface extends BaseRepositoryInterface
{
    public function getAllCollection($offset, $limit);
}
