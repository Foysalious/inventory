<?php namespace App\Http\Controllers;

use App\Http\Requests\SkuStockAddRequest;
use App\Http\Requests\SkuStockUpdateRequest;
use App\Services\Sku\SkuService;
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
     * @param int $partner_id
     * @param SkuStockUpdateRequest $request
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
