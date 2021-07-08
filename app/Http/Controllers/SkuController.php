<?php namespace App\Http\Controllers;

use App\Http\Requests\SkuStockAddRequest;
use App\Http\Requests\SkuStockUpdateRequest;
use App\Services\Sku\SkuService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SkuController extends Controller
{
    /**
     * @var SkuService $skuService
     */
    private SkuService $skuService;

    public function __construct(SkuService $skuService)
    {
        $this->skuService = $skuService;
    }

    public function index($partner, Request $request)
    {
        return $this->skuService->getSkuList($partner, $request);
    }

    public function getSkusByProductIds(Request $request)
    {
        return $this->skuService->getSkusByProductIds($request->product_ids);
    }

    /**
     *     /**
     *
     * * @OA\PUT(
     *      path="/api/v1/partners/{partner}/stock-update",
     *      operationId="stockUpdate",
     *      tags={"Stock Update"},
     *      summary="Partner Product's Stock Update for order",
     *      description="update sku stock of a product on order create/update",
     *      @OA\Parameter(name="partner", description="partner id", required=true, in="path", @OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          @OA\MediaType(mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(property="id", type="Integer"),
     *                  @OA\Property(property="product_id", type="Integer"),
     *                  @OA\Property(property="operation", type="Text"),
     *                  @OA\Property(property="quantity", type="Integer"),
     *             )
     *         )
     *      ),
     *      @OA\Response(response=200, description="Successful"),
     *      @OA\Response(response=404, description="message: Sku Not Found!"),
     *      @OA\Response(response=403, description="Forbidden")
     *     )
     *
     * @param SkuStockUpdateRequest $request
     * @return JsonResponse
     */
    public function updateSkuStock(SkuStockUpdateRequest $request)
    {
        return $this->skuService->updateSkuStockForOrder($request);
    }

    public function addStock(int $partner_id, int $product_id, SkuStockAddRequest $request)
    {
        return $this->skuService->addStock($partner_id,$product_id,$request);
    }
}
