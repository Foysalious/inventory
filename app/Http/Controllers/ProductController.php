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
