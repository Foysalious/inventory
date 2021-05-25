<?php namespace App\Services\Webstore\Product;

use App\Http\Resources\Webstore\ProductResource;
use App\Http\Resources\Webstore\ProductSearchResultResource;
use App\Interfaces\ProductRepositoryInterface;
use App\Services\Product\ProductCombinationService;
use App\Traits\ResponseAPI;
use Illuminate\Http\JsonResponse;
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

    /**
     * @param string $searchKey
     * @param int $partnerId
     * @param int $limit
     * @return JsonResponse
     */
    public function search(string $searchKey, int $partnerId, int $limit = 5): JsonResponse
    {
        $products =  $this->productRepositoryInterface->searchProductFromWebstore($searchKey, $partnerId, $limit);
        if($products->isEmpty())
            return $this->error("No products found", 404);
        $products = ProductSearchResultResource::collection($products);
        return $this->success("Successful", ['products' => $products]);
    }

    public function getDetails($partner_id, $product_id)
    {
        $general_details = $this->productRepositoryInterface->find($product_id);
        if ($general_details->partner_id != $partner_id)
            return $this->error("This product does not belongs to this partner", 403);
        list($options, $combinations) = $this->productCombinationService->setProduct($general_details)->getCombinationData();
        $product = new ProductResource($general_details);
        return $this->success('Successful', ['product' => $product], 200);
    }



}
