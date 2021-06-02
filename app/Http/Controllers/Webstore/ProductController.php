<?php namespace App\Http\Controllers\Webstore;

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

    public function index($partner, Request $request)
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
