<?php namespace App\Services\Webstore\Product;

use App\Interfaces\ProductRepositoryInterface;
use App\Traits\ResponseAPI;

class ProductService
{
    use ResponseAPI;

    private ProductRepositoryInterface $productRepositoryInterface;

    public function __construct(ProductRepositoryInterface $productRepositoryInterface)
    {
        $this->productRepositoryInterface = $productRepositoryInterface;
    }

    public function search($searchKey, $partnerId, $limit = 5, $offset = 0)
    {
        $products =  $this->productRepositoryInterface->searchProductFromWebstore($searchKey, +$partnerId, 5);
        if (count($products->toArray()) > 0) return $this->success("Successful", ['products' => $products->toArray()]);
        return $this->error("No products found", 404);
    }

}
