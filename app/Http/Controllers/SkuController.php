<?php namespace App\Http\Controllers;


use App\Services\Sku\SkuService;
use Illuminate\Http\Request;

class SkuController extends Controller
{
    /**
     * @var SkuService
     */
    private SkuService $skuService;

    public function __construct(SkuService $skuService)
    {
        $this->skuService = $skuService;
    }

    public function index($partner, Request $request)
    {
       return $this->skuService->getSkuList($partner,$request);

    }
}
