<?php namespace App\Services\Webstore\Product;

use App\Exceptions\ProductNotFoundException;
use App\Http\Resources\Webstore\ProductResource;
use App\Http\Resources\Webstore\ProductSearchResultResource;
use App\Interfaces\ProductRepositoryInterface;
use App\Services\Product\ProductCombinationService;
use App\Traits\ResponseAPI;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class ProductService
{
    use ResponseAPI;

    private ProductRepositoryInterface $productRepositoryInterface;
    private ProductCombinationService $productCombinationService;
    private ProductList $productList;

    public function __construct(ProductRepositoryInterface $productRepositoryInterface, ProductCombinationService $productCombinationService, ProductList $productList)
    {
        $this->productRepositoryInterface = $productRepositoryInterface;
        $this->productCombinationService = $productCombinationService;
        $this->productList = $productList;

    }


    /**
     * @param $partner_id
     * @param Request $request
     * @return JsonResponse
     * @throws ProductNotFoundException
     */
    public function getProducts(int $partner_id, Request $request): JsonResponse
    {
        list($offset, $limit) = calculatePagination($request);
        $category_ids = !is_array($request->category_ids) ? json_decode($request->category_ids,1) : $request->category_ids;
        $collection_ids = !is_array($request->collection_ids) ? json_decode($request->collection_ids,1) : $request->collection_ids;
        $price_range = !is_array($request->price_range) ? json_decode($request->price_range,1) : $request->price_range;
        $ratings = !is_array($request->ratings) ? json_decode($request->ratings,1) : $request->ratings;
        $this->productList->setPartnerId($partner_id)
            ->setCategoryIds($category_ids)
            ->setCollectionIds($collection_ids)
            ->setPriceRange($price_range)
            ->setRatings($ratings)
            ->setOffset($offset)
            ->setLimit($limit);
        list($product_count,$products) = $this->productList->get();
        if ($request->has('order_by')) {
            $order = ($request->order == 'desc') ? 'sortByDesc' : 'sortBy';
            $products = $products->$order($request->order_by, SORT_NATURAL | SORT_FLAG_CASE);
        }
        return $this->success('Successful', ['product_count' => $product_count,'products' => $products], 200);
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
        $products = $products->filter(function ($product){
           $stock = $product->stock();
            if($stock != null && $stock > 0) {
                return $product;
            }
        });
        $products = ProductSearchResultResource::collection($products);
        return $this->success("Successful", ['products' => $products]);
    }

    public function getDetails($partner_id, $product_id)
    {
        $general_details = $this->productRepositoryInterface->find($product_id);
        if (!$general_details)
            return $this->error("Product is not found", 404);
        if ($general_details->partner_id != $partner_id)
            return $this->error("This product does not belongs to this partner", 403);
        list($options, $combinations) = $this->productCombinationService->setProduct($general_details)->getCombinationData();
        $product = new ProductResource($general_details);
        return $this->success('Successful', ['product' => $product], 200);
    }
}
