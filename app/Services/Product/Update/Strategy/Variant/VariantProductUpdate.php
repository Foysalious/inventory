<?php namespace App\Services\Product\Update\Strategy\Variant;


use App\Interfaces\DiscountRepositoryInterface;
use App\Interfaces\SkuChannelRepositoryInterface;
use App\Interfaces\SkuRepositoryInterface;
use App\Services\Discount\Creator as DiscountCreator;
use App\Services\Product\CheckProductPublishAccess;
use App\Services\Product\CombinationCreator;
use App\Services\Product\ProductChannelCreator;
use App\Services\Product\ProductOptionCreator;
use App\Services\Product\ProductOptionValueCreator;
use App\Services\Product\ProductStockBatchUpdater;
use App\Services\Product\Update\Strategy\ProductUpdate;
use App\Services\Sku\Creator as SkuCreator;
use App\Services\SkuBatch\Updater as SkuStockUpdater;

abstract class VariantProductUpdate extends ProductUpdate
{
    protected bool $hasVariants;

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
        protected ProductOptionValueCreator $productOptionValueCreator)
    {
        parent::__construct($skuRepository, $skuCreator, $skuStockUpdater, $skuChannelRepository, $productStockBatchUpdater, $discountRepository, $discountCreator, $productChannelCreator, $productPublishAccess);
    }

    public abstract function update();

    protected function createCombination(int $sku_id, array $product_option_value_ids)
    {
        $combinations = collect($product_option_value_ids)->map(function ($product_option_value_id) use ($sku_id) {
            return [
                'product_option_value_id' => $product_option_value_id,
                'sku_id' => $sku_id
            ];
        });
        return $this->combinationCreator->setData($combinations->toArray())->store();
    }

    protected function createProductOptions(int $product_id, string $option_name)
    {
        return $this->productOptionCreator->setProductId($product_id)->setOptionName($option_name)->create();
    }

    protected function createProductOptionValues(int $product_option_id, string $value_name)
    {
        return $this->productOptionValueCreator->setProductOptionId($product_option_id)->setValueName($value_name)->create();
    }


}
