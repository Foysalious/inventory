<?php namespace App\Interfaces;


use App\Models\Product;

interface ProductRepositoryInterface extends BaseRepositoryInterface
{
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
    public function getProductsByPartnerQuery(int $partnerId);
    public function getStockDataForAccounting(Product $product);
}
