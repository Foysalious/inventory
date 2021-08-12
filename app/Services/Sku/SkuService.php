<?php namespace App\Services\Sku;

use App\Http\Requests\SkuStockAddRequest;
use App\Http\Requests\SkuStockUpdateRequest;
use App\Http\Resources\WebstoreProductResource;
use App\Http\Resources\SkuResource;
use App\Interfaces\SkuBatchRepositoryInterface;
use App\Interfaces\SkuRepositoryInterface;
use App\Services\BaseService;
use App\Services\SkuBatch\SkuBatchDto;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
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
     * @param $partner_id
     * @param Request $request
     * @return JsonResponse
     */
    public function getSkuList($partner_id, Request $request)
    {
        $channel_id = $request->channel_id;
        $sku_ids = json_decode($request->skus, true);
        $with_deleted = $request->with_deleted ?? null;

        if (!is_null($with_deleted) && $sku_ids) {
            /** @var Collection $skus */
            $skus = $this->skuRepository->getSkusWithTrashed($sku_ids,$partner_id)->keyBy('id');
            return $this->success('Successful', ['skus' => $skus]);
        }
        if ($channel_id && $sku_ids) {
            $skus = $this->skuRepository->getSkusByIdsAndChannel($sku_ids, $channel_id, $partner_id);
        }
        $skus = $skus ? SkuResource::collection($skus) : [];
        return $this->success('Successful', ['skus' => $skus]);
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
     * @return JsonResponse
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
     * @return JsonResponse
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
