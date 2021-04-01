<?php namespace App\Services\Sku;

use App\Http\Resources\ProductResource;
use App\Http\Resources\SkuResource;
use App\Interfaces\SkuRepositoryInterface;
use App\Services\BaseService;
use Illuminate\Http\Request;

class SkuService extends BaseService
{
    /**
     * @var SkuRepositoryInterface
     */
    private SkuRepositoryInterface $skuRepository;

    public function __construct(SkuRepositoryInterface $skuRepository)
    {
        $this->skuRepository = $skuRepository;
    }

    public function getSkuList($partner, Request $request)
    {
        $sku_channel = $request->channel_id;
        $skus = json_decode($request->skus,true);
        if($sku_channel && $skus)
            $resources =  $this->skuRepository->getSkusByIdsAndChannel($sku_channel,$skus);

        $product = SkuResource::collection($resources);
        return $this->success('Successful', ['products' => $product], 200);

    }

}
