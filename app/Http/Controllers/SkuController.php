<?php namespace App\Http\Controllers;

use App\Services\Sku\SkuService;
use Illuminate\Http\Request;

class SkuController extends Controller
{
    private $skuService;

    /**
     * SkuController constructor.
     * @param $skuService
     */
    public function __construct(SkuService $skuService)
    {
        $this->skuService = $skuService;
    }

    public function getSkusByProductIds(Request $request)
    {
        return $this->skuService->getSkusByProductIds($request->product_ids);
    }
}
