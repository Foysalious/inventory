<?php namespace App\Http\Controllers\Webstore;

use App\Http\Controllers\Controller;
use App\Services\Webstore\Product\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class ProductController extends Controller
{
    public function search(Request $request, ProductService $productService)
    {
        $this->validate($request, ['search' => 'required|string', 'partner_id' => 'required|numeric']);
        return $productService->search($request->searchKey, +$request->partner_id);
    }

    /**
     *
     * * @OA\Get(
     *      path="api/v1/partners/{partner}/product-details/{product}",
     *      operationId="getCategory",
     *      tags={"Partners Category API"},
     *      summary="Get Category Tree List by Partner",
     *      description="",
     *      @OA\Parameter(name="partner", description="partner id", required=true, in="path", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="product", description="product id", required=true, in="path", @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful operation", @OA\JsonContent(ref="")),
     *     )
     * @param $partner_id
     * @param $product_id
     * @return JsonResponse
     */
    public function getProductInformation(Request $request, $partner_id, $product_id, ProductService $productService)
    {
        return $productService->getProductInformation($request, $partner_id, $product_id);
    }

}
