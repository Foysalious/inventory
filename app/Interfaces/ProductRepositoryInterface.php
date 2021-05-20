<?php namespace App\Interfaces;


interface ProductRepositoryInterface extends BaseRepositoryInterface
{
    public function getProductsByPartnerId($partnerId, $offset = 0, $limit = 50, $searchKey = null);
    public function getProductsByCategoryId($category_id);
    public function productChannelPrice($productId);
    public function productInformation($productId);

    /**
     * @param string $searchKey
     * @param int $partnerId
     * @param int $limit
     * @param int $offset
     * @return mixed
     */
    public function searchProductFromWebstore(string $searchKey, int $partnerId, $limit = 10, $offset = 0);
}
