<?php namespace App\Services\Product\Update\Strategy\Variant;


use App\Interfaces\CombinationRepositoryInterface;
use App\Interfaces\DiscountRepositoryInterface;
use App\Interfaces\PartnerRepositoryInterface;
use App\Interfaces\ProductOptionValueRepositoryInterface;
use App\Interfaces\SkuChannelRepositoryInterface;
use App\Interfaces\SkuRepositoryInterface;
use App\Repositories\SkuBatchRepository;
use App\Services\AccessManager\AccessManager;
use App\Services\Discount\Creator as DiscountCreator;
use App\Services\Discount\Types;
use App\Services\Product\CheckProductPublishAccess;
use App\Services\Product\CombinationCreator;
use App\Services\Product\CombinationUpdateDetailsObject;
use App\Services\Product\ProductChannelCreator;
use App\Services\Product\ProductOptionCreator;
use App\Services\Product\ProductOptionValueCreator;
use App\Services\Product\ProductStockBatchUpdater;
use App\Services\Product\ProductUpdateDetailsObjects;
use App\Services\Product\Update\Strategy\ProductUpdateStrategy;
use App\Services\Sku\Creator as SkuCreator;
use App\Services\SkuBatch\Updater as SkuStockUpdater;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;


class ValuesUpdate extends VariantProductUpdate
{
    public function __construct(
        protected SkuRepositoryInterface $skuRepository,
        protected SkuCreator $skuCreator,
        protected SkuStockUpdater $skuStockUpdater,
        protected SkuChannelRepositoryInterface $skuChannelRepository,
        protected ProductStockBatchUpdater $productStockBatchUpdater,
        protected DiscountRepositoryInterface $discountRepository,
        protected DiscountCreator $discountCreator,
        protected ProductChannelCreator $productChannelCreator,
        protected CheckProductPublishAccess $productPublishAccess,
        protected CombinationCreator $combinationCreator,
        protected ProductOptionCreator $productOptionCreator,
        protected ProductOptionValueCreator $productOptionValueCreator,
        protected ProductOptionValueRepositoryInterface $productOptionValueRepository,
        protected CombinationRepositoryInterface $combinationRepository,
        protected SkuBatchRepository $skuBatchRepository)
    {
        parent::__construct(
            $skuRepository,
            $skuCreator,
            $skuStockUpdater,
            $skuChannelRepository,
            $productStockBatchUpdater,
            $discountRepository,
            $discountCreator,
            $productChannelCreator,
            $productPublishAccess,
            $combinationCreator,
            $productOptionCreator,
            $productOptionValueCreator);
    }

    /**
     * @throws UnknownProperties
     */
    public function update()
    {
        $this->deleteDiscardedCombinations();
        $this->operationsForValueAdd();
        $this->deleteProductChannels();
        $this->createProductChannel($this->product->id, $this->channels);
    }

    protected function deleteDiscardedCombinations()
    {
        if ($this->getDeletedValues()) {
            $this->productOptionValueRepository->whereIn('id', $this->getDeletedValues())->delete();
        /** @var array $skus_to_delete */
            $skus_to_delete = $this->combinationRepository->whereIn('product_option_value_id', $this->deletedValues)->pluck('sku_id');
            $this->deleteSkusStockBatch($skus_to_delete);
            $skus_channels_to_delete = $this->skuChannelRepository->whereIn('sku_id', $skus_to_delete)->pluck('id');
            $this->skuRepository->whereIn('id', $skus_to_delete)->delete();
            $this->skuChannelRepository->whereIn('sku_id', $skus_to_delete)->delete();
            $this->combinationRepository->whereIn('product_option_value_id', $this->deletedValues)->delete();
            $this->deleteSkuChannelDiscount($skus_channels_to_delete);
        }
    }

    protected function deleteSkusStockBatch(array $sku_ids)
    {
        $this->skuBatchRepository->whereIn('sku_id', $sku_ids)->delete();
    }

    protected function deleteSkuChannelDiscount(array $skus_channels_to_delete)
    {
        $this->discountRepository->whereIn('type_id', $skus_channels_to_delete)->where('type', Types::SKU_CHANNEL)->delete();
    }

    /**
     * @throws UnknownProperties
     */
    protected function operationsForValueAdd()
    {
        foreach ($this->updateDataObjects as $productDetailObject) {
            $combinations = $productDetailObject->getCombination();
            $sku_channels = $productDetailObject->getChannelData();

            /** @var bool $is_old */
            /** @var ?int $related_skus */
            list($is_old, $related_skus) = $this->checkAndApplyOperationIfOldCombination($combinations, $productDetailObject);
            if ($is_old) {
                $this->updateSkuChannels($sku_channels, $related_skus);
                continue;
            }

            $product_option_value_ids = [];
            foreach ($combinations as $combination) {
                $option_name = $combination->getOptionName();
                $product_option = $this->createProductOptions($this->product->id, $option_name);
                $value_name = $combination->getOptionValueName();
                $product_option_value = $this->createProductOptionValues($product_option->id, $value_name);
                array_push($product_option_value_ids, $product_option_value->id);

            }
            $sku = $this->createSku($this->product, $productDetailObject);
            $this->createSkuChannels($sku, $sku_channels);
            $this->createCombination($sku->id, $product_option_value_ids);
            $this->productStockBatchUpdater->createBatchStock($sku, $productDetailObject, $this->accountingInfo);
        }
    }

    protected function operationsForOldValues()
    {
        foreach ($this->updateDataObjects as $productDetailObject) {
            $combinations = $productDetailObject->getCombination();
            $sku_channels = $productDetailObject->getChannelData();
            $related_skus = $this->checkAndApplyOperationForOldCombination($combinations, $productDetailObject);
            $this->updateSkuChannels($sku_channels, $related_skus);
        }
    }

    /**
     * @param CombinationUpdateDetailsObject[] $combination
     * @param ProductUpdateDetailsObjects $sku
     * @return int|null
     */
    protected function checkAndApplyOperationForOldCombination(array $combination, ProductUpdateDetailsObjects $sku): ?int
    {
        $old_product_option_value_ids = [];
        foreach ($combination as $option_values) {
            array_push($old_product_option_value_ids, $option_values->getOptionValueId());
        }
        $old_sku = $this->combinationRepository->whereIn('product_option_value_id', $old_product_option_value_ids)->pluck('sku_id')->first();
        $this->productStockBatchUpdater->updateBatchStock($old_sku, $sku);
        return $old_sku;
    }

    /**
     * @param CombinationUpdateDetailsObject[] $combination
     * @param ProductUpdateDetailsObjects $sku
     * @return array
     */
    protected function checkAndApplyOperationIfOldCombination(array $combination, ProductUpdateDetailsObjects $sku): array
    {
        $is_old = !is_null($combination[0]->getOptionValueId());
        $old_sku = null;
        if ($is_old) {
            $old_product_option_value_ids = [];
            foreach ($combination as $option_values) {
                array_push($old_product_option_value_ids, $option_values->getOptionValueId());
            }
            $old_sku = $this->combinationRepository->whereIn('product_option_value_id', $old_product_option_value_ids)->pluck('sku_id')->first();
            $this->productStockBatchUpdater->updateBatchStock($old_sku, $sku);
        }
        return [$is_old, $old_sku];
    }


}
