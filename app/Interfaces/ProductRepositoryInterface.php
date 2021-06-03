<?php namespace App\Interfaces;


interface ProductRepositoryInterface extends BaseRepositoryInterface
{
    public function getProductsByPartnerId(int $partnerId, int $offset = 0, int $limit = 50);
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
