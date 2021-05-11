<?php namespace App\Interfaces;


interface ProductRepositoryInterface extends BaseRepositoryInterface
{
    public function getProductsByPartnerId($partnerId, $offset = 0, $limit = 50, $searchKey = null);
    public function getProductsByCategoryId($category_id);
    public function productChannelPrice($productId);
}
