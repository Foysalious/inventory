<?php namespace App\Services\Webstore\Product;

use App\Http\Resources\Webstore\WebstoreProductResource;
use App\Interfaces\ProductRepositoryInterface;
use App\Services\Product\ProductCombinationService;
use App\Traits\ResponseAPI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class ProductService
{
    use ResponseAPI;

    private ProductRepositoryInterface $productRepositoryInterface;
    private ProductCombinationService $productCombinationService;

    public function __construct(ProductRepositoryInterface $productRepositoryInterface, ProductCombinationService $productCombinationService)
    {
        $this->productRepositoryInterface = $productRepositoryInterface;
        $this->productCombinationService = $productCombinationService;
    }

    public function search($searchKey, $partnerId, $limit = 5)
    {
        $products =  $this->productRepositoryInterface->searchProductFromWebstore($searchKey, +$partnerId, $limit);
        if (count($products->toArray()) > 0) return $this->success("Successful", ['products' => $products->toArray()]);
        return $this->error("No products found", 404);
    }

    public function getProductInformation($request, $partner_id, $product_id)
    {


        $general_details = $this->productRepositoryInterface->find($product_id);

        if ($general_details->partner_id != $partner_id)
            return $this->error("This product does not belongs to this partner", 403);
        list($options, $combinations) = $this->productCombinationService->setProduct($general_details)->getCombinationData();


        $product = new WebstoreProductResource($general_details);


        return $this->success('Successful', ['product' => $product], 200);
    }



}
