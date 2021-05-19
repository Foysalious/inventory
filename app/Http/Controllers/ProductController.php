<?php namespace App\Http\Controllers;

use App\Exceptions\ProductNotFoundException;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Services\Product\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /** @var ProductService */
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     *
     * * @OA\Get(
     *      path="/api/v1/partners/{partner}/products",
     *      operationId="getProducts",
     *      tags={"Partners Products API"},
     *      summary="Get Products List for POS by Partner",
     *      description="",
     *      @OA\Parameter(name="partner", description="partner id", required=true, in="path", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="master_category_ids", description="master category ids", required=false, in="query", @OA\Schema(type="array", @OA\Items(type="integer")) ),
     *      @OA\Parameter(name="category_ids", description="category ids", required=false, in="query", @OA\Schema(type="array", @OA\Items(type="integer")) ),
     *      @OA\Parameter(name="updated_after", description="products updated after date", required=false, in="query", @OA\Schema(type="string") ),
     *      @OA\Parameter(name="offset", description="pagination offset", required=false, in="query", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="limit", description="pagination limit", required=false, in="query", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="is_published_for_webstore", description="publication filter for webstore", required=false, in="query", @OA\Schema(type="integer", enum={0,1}) ),
     *      @OA\Response(response=200, description="Successful operation", @OA\JsonContent(ref="")),
     *      @OA\Response(response=404, description="message: স্টকে কোন পণ্য নেই! প্রয়োজনীয় তথ্য দিয়ে স্টকে পণ্য যোগ করুন।"),
     *      @OA\Response(response=403, description="Forbidden")
     *     )
     * @param $partner
     * @param Request $request
     * @return JsonResponse
     *
     * @throws ProductNotFoundException
     */
    public function index($partner, Request $request)
    {
        return $this->productService->getProducts($partner, $request);
    }

    /**
     * @param $partner
     * @param Request $request
     * @return JsonResponse
     * @throws ProductNotFoundException
     */
    public function getWebstoreProducts($partner, Request $request)
    {
        return $this->productService->getWebstoreProducts($partner, $request);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param $partner
     * @param ProductRequest $request
     * @return JsonResponse
     */
    public function store($partner, ProductRequest $request)
    {
        return $this->productService->create($partner, $request);
    }

    /**
     * Display the specified resource.
     *
     * @param $product
     * @return JsonResponse
     */
    public function show($partner, $product)
    {
        return $this->productService->getDetails($partner, $product);
    }

    /**
     * @param $partner
     * @param $product
     * @param ProductUpdateRequest $request
     * @return JsonResponse
     */
    public function update($partner, $product, ProductUpdateRequest $request)
    {
        return $this->productService->update($product, $request, $partner);
    }

    public function destroy($partner, $product)
    {
        return $this->productService->delete($partner,$product);
    }
}
