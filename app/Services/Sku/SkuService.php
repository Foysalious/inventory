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
        $sku_channel = $request->channel_id;
        $skus = json_decode($request->skus, true);
        if ($sku_channel && $skus)
            $resources = $this->skuRepository->getSkusByIdsAndChannel($sku_channel, $skus);
        else
            $resources = $this->skuRepository->getSkusByPartnerId($partner, $offset, $limit);

        $product = SkuResource::collection($resources);
        return $this->success('Successful', ['products' => $product], 200);
    }

    public function getSkusByProductIds($productIds)
    {
        $skus = $this->skuRepository->whereIn('product_id', $productIds)
            ->pluck('id', 'product_id');
        return $this->success('Successful', ['skus' => $skus], 200);
    }
}
