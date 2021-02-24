<?php namespace App\Http\Controllers;

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
     * @param $partner
     * @param Request $request
     * @return JsonResponse
     */
    public function index($partner, Request $request)
    {
        return $this->productService->getProductList($partner, $request);
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
        return $this->productService->getDetails($product);
    }

    public function update($partner, $product, ProductUpdateRequest $request)
    {
        return $this->productService->update($product, $request);
    }
}
