<?php namespace App\Http\Controllers\Webstore;

use App\Exceptions\ProductNotFoundException;
use App\Http\Controllers\Controller;
use App\Services\Webstore\Product\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;


class ProductController extends Controller
{

    private ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }


    /**
     *
     * * @OA\Get(
     *      path="/api/v1/webstore/partners/{partner}/products",
     *      operationId="index",
     *      tags={"Partners Webstore Products API"},
     *      summary="Get Products List for Webstore by Partner",
     *      description="",
     *      @OA\Parameter(name="partner", description="partner id", required=true, in="path", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="offset", description="pagination offset", required=false, in="query", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="limit", description="pagination limit", required=false, in="query", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="limit", description="pagination limit", required=false, in="query", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="category_ids", description="category ids", required=false, in="query", @OA\Schema(type="array", @OA\Items(type="integer")) ),
     *      @OA\Parameter(name="collection_ids", description="collection ids", required=false, in="query", @OA\Schema(type="array", @OA\Items(type="integer")) ),
     *      @OA\Parameter(name="ratings", description="ratings", required=false, in="query", @OA\Schema(type="array", @OA\Items(type="integer")) ),
     *      @OA\Parameter(name="price_range", description="price range", required=false, in="query", @OA\Schema(type="array", @OA\Items(type="integer")) ),
     *      @OA\Parameter(name="order_by", description="ratings", required=false, in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="order", description="price range", required=false, in="query", @OA\Schema(type="string")),
     *      @OA\Response(response=200, description="Successful operation",
     *          @OA\JsonContent(
     *          type="object",
     *          example={
     *               "message": "Successful",
     *               "product_count": 21,
     *                "products": {
     *                     {
     *                    "id": 1000328,
     *                   "category_id": 10052,
     *                   "collection_id": {},
     *                    "name": "Vegetable",
     *                    "rating": null,
     *                   "rating_count": null,
     *                   "app_thumb": "https://s3.ap-south-1.amazonaws.com/cdn-shebadev/images/pos/services/thumbs/default.jpg",
     *                   "original_price": 99.75,
     *                    "discounted_price": 99.75,
     *                   "discount_percentage": 0
     *                   }}
     *           },
     *       ),
     *      ),
     *      @OA\Response(response=404, description="message: স্টকে কোন পণ্য নেই! প্রয়োজনীয় তথ্য দিয়ে স্টকে পণ্য যোগ করুন।"),
     *     )
     * @param $partner
     * @param Request $request
     * @return JsonResponse
     *
     * @throws ProductNotFoundException
     */
    public function index($partner, Request $request): JsonResponse
    {
        return $this->productService->getProducts($partner, $request);
    }
    /**
     *
     * * @OA\Get(
     *      path="/api/v1/partners/{partner}/products/search",
     *      operationId="search",
     *      tags={"Product Search API"},
     *      summary="search by partnerId and searchKey",
     *      description="",
     *      @OA\Parameter(name="partner", description="partner id", required=true, in="path", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="search_key", description="search key", required=true, in="query", @OA\Schema(type="string")),
     *      @OA\Response(response=200, description="Successful operation", @OA\JsonContent(ref="")),
     *   )
     *
     * @throws ValidationException
     */
    public function search(Request $request ,$partner_id, ProductService $productService): JsonResponse
    {
        $this->validate($request, ['search_key' => 'required|string']);
        return $productService->search($request->search_key, +$partner_id);
    }

    /**
     *
     * * @OA\Get(
     *      path="/api/v1/partners/{partner}/product-details/{product}",
     *      operationId="getCategory",
     *      tags={"Partners Category API"},
     *      summary="Get Product Information By Partner and Product ID",
     *      description="",
     *      @OA\Parameter(name="partner", description="partner id", required=true, in="path", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="product", description="product id", required=true, in="path", @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful operation", @OA\JsonContent(ref="")),
     *     )
     * @param $partner_id
     * @param $product_id
     * @return JsonResponse
     */
    public function show(Request $request, $partner_id, $product_id, ProductService $productService)
    {
        return $productService->getDetails($partner_id, $product_id);
    }

}
