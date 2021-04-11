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

    /**
     * @param $partner
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSkuList($partner, Request $request)
    {
        list($offset, $limit) = calculatePagination($request);
        $channel_id = $request->channel_id;
        $skus = json_decode($request->skus, true);
        if ($channel_id && $skus)
            $skus = $this->skuRepository->getSkusByIdsAndChannel($skus, $channel_id,);
        else
            $skus = $this->skuRepository->getSkusByPartnerId($partner, $offset, $limit);

        $sku_with_sku_details = $skus->map(function ($sku) use ($channel_id) {
            return $sku->sku_details = $this->getSkuDetails($channel_id, $sku->id);
        })->first();

        $skus = SkuResource::collection($sku_with_sku_details);
        return $this->success('Successful', ['skus' => $skus], 200);
    }

    public function getSkuDetails($channel_id,$sku_id)
    {
        return $this->skuRepository->getSkuDetails($channel_id,$sku_id);
    }

}
