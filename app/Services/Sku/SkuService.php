<?php namespace App\Services\Sku;

use App\Http\Requests\SkuStockAddRequest;
use App\Http\Requests\SkuStockUpdateRequest;
use App\Http\Resources\WebstoreProductResource;
use App\Http\Resources\SkuResource;
use App\Interfaces\SkuBatchRepositoryInterface;
use App\Interfaces\SkuRepositoryInterface;
use App\Services\BaseService;
use App\Services\SkuBatch\SkuBatchDto;
use Illuminate\Http\Request;
use App\Services\SkuBatch\Creator as SkuBatchCreator;
use App\Services\SkuBatch\UpdaterForOrder as SkuBatchUpdaterForOrder;

class SkuService extends BaseService
{
    /**
     * @var SkuRepositoryInterface
     */
    private SkuRepositoryInterface $skuRepository;

    public function __construct(
        SkuRepositoryInterface $skuRepository,
        protected SkuBatchCreator $skuBatchCreator,
        protected SkuBatchUpdaterForOrder $skuBatchUpdaterForOrder
    )
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
            $skus = $this->skuRepository->getSkusByIdsAndChannel($skus, $channel_id);
        else
            $skus = $this->skuRepository->getSkusByPartnerId($partner, $offset, $limit);


        $skus->each(function ($sku) use ($channel_id) {
             $sku->sku_details = $this->getSkuDetails($channel_id, $sku->id);
        });


        $skus = SkuResource::collection($skus);
        return $this->success('Successful', ['skus' => $skus], 200);
    }

    public function getSkuDetails($channel_id,$sku_id)
    {
        return $this->skuRepository->getSkuDetails($channel_id,$sku_id);
    }

    public function getSkusByProductIds($productIds)
    {
        $skus = $this->skuRepository->whereIn('product_id', $productIds)
            ->pluck('id', 'product_id');
        return $this->success('Successful', ['skus' => $skus], 200);
    }

    /**
     * @param SkuStockUpdateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateSkuStockForOrder(SkuStockUpdateRequest $request)
    {
        $sku = $this->skuRepository->where('id', $request->id)->where('product_id', $request->product_id)->first();
        if (!$sku)  return $this->error("sku not found under this product", 403);

        if ($request->operation == StockOperationType::DECREMENT) {
            $this->skuBatchUpdaterForOrder->setSku($sku)->setQuantity($request->quantity)->stockDecrease();
        }
        if ($request->operation == StockOperationType::INCREMENT) {
            $this->skuBatchUpdaterForOrder->setSku($sku)->setQuantity($request->quantity)->stockIncrease();
        }
        return $this->success('Successful', null, 200);
    }

    /**
     * @param int $partner_id
     * @param int $product_id
     * @param SkuStockAddRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Spatie\DataTransferObject\Exceptions\UnknownProperties
     */
    public function addStock(int $partner_id, int $product_id, SkuStockAddRequest $request)
    {
        $sku = $this->skuRepository->where('product_id', $product_id)->where('id', $request->sku_id)->first();
        if(!$sku) {
            return $this->error("Variation not found under this product", 403);
        }
        $this->skuBatchCreator->create(new SkuBatchDto([
            'sku_id' => $request->sku_id,
            'cost' => $request->cost,
            'stock' => $request->stock,
        ]));

        return $this->success('Successful', null, 200);
    }
}
