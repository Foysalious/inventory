<?php namespace App\Services\Product\Update;


use App\Interfaces\CombinationRepositoryInterface;
use App\Interfaces\OptionRepositoryInterface;
use App\Interfaces\ProductChannelRepositoryInterface;
use App\Interfaces\ProductOptionRepositoryInterface;
use App\Interfaces\ProductOptionValueRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use App\Interfaces\SkuRepositoryInterface;
use App\Interfaces\ValueRepositoryInterface;
use App\Models\Product;
use App\Services\Product\ProductUpdateDetailsObjects;
use App\Services\Product\Update\Strategy\NonVariant\NonVariantProductUpdate;
use App\Services\Product\Update\Strategy\ProductUpdateStrategy;
use App\Services\Product\Update\Strategy\Variant\OptionsAdd;
use App\Services\Product\Update\Strategy\Variant\OptionsDelete;
use App\Services\Product\Update\Strategy\Variant\OptionsUpdate;
use App\Services\Product\Update\Strategy\Variant\ValuesAdd;
use App\Services\Product\Update\Strategy\Variant\ValuesDelete;
use App\Services\Product\Update\Strategy\Variant\ValuesUpdate;

class StrategyFactory
{
    public function __construct(
        protected ProductRepositoryInterface $productRepositoryInterface,
        protected OptionRepositoryInterface $optionRepositoryInterface,
        protected ValueRepositoryInterface  $valueRepositoryInterface,
        protected ProductOptionRepositoryInterface $productOptionRepositoryInterface,
        protected ProductOptionValueRepositoryInterface $productOptionValueRepositoryInterface,
        protected CombinationRepositoryInterface  $combinationRepositoryInterface,
        protected ProductChannelRepositoryInterface $productChannelRepositoryInterface,
        protected SkuRepositoryInterface $skuRepositoryInterface,
        private array|null $deletedValues = null){}

    /**
     * @param Product $product
     * @param ProductUpdateDetailsObjects[] $skus
     * @param bool $has_variant
     * @return ProductUpdateStrategy
     */
    public function getStrategy(Product $product, array $skus, bool $has_variant): ProductUpdateStrategy
    {
//        /** @var bool $is_new_values_added */
//        /** @var array $updatedValues */
//        list($is_new_values_added, $updatedValues) = $this->checkIsValuesAdded($skus);
//        list($is_values_deleted, $this->deletedValues) = $this->checkIsValuesDeleted($product, $updatedValues);
//        $created_with_variants = $this->productOptionRepositoryInterface->where('product_id',$product->id)->count() > 0;
//        if(!$created_with_variants && !$has_variant)
//            return app(NonVariantProductUpdate::class);
//        elseif(!$created_with_variants && $has_variant)
//            return app(OptionsAdd::class);
//        elseif($created_with_variants && !$has_variant)
//            return app(OptionsDelete::class);
//        elseif($this->checkIsOptionChanged($skus[0]->getCombination()))
//            return app(OptionsUpdate::class);
//        elseif ($is_new_values_added && $is_values_deleted)
//            return app(ValuesUpdate::class);
//        elseif ($is_new_values_added && !$is_values_deleted)
//            return app(ValuesAdd::class);
//        else
//            return app(ValuesDelete::class);

        $deleted_values = null;
        $created_with_variants = $this->productOptionRepositoryInterface->where('product_id',$product->id)->count() > 0;
        if(!$created_with_variants && !$has_variant) {
            $this->deletedValues = $deleted_values;
            return app(NonVariantProductUpdate::class);
        } elseif (!$created_with_variants && $has_variant) {
            $this->deletedValues = $deleted_values;
            return app(OptionsAdd::class);
        } elseif ($created_with_variants && !$has_variant) {
            $this->deletedValues = $deleted_values;
            return app(OptionsDelete::class);
        } elseif ($this->checkIsOptionChanged($skus[0]->getCombination())) {
            $this->deletedValues = $deleted_values;
            return app(OptionsUpdate::class);
        }
        list($is_new_values_added, $updatedValues) = $this->checkIsValuesAdded($skus);
        list($is_values_deleted,$deleted_values) = $this->checkIsValuesDeleted($product, $updatedValues);
        if ($is_new_values_added && $is_values_deleted) {
            $this->deletedValues = $deleted_values;
            return app(ValuesUpdate::class);
        } elseif ($is_new_values_added && !$is_values_deleted) {
            $this->deletedValues = $deleted_values;
            return app(ValuesAdd::class);
        } else {
            $this->deletedValues = $deleted_values;
            return app(ValuesDelete::class);
        }

    }

    private function checkIsValuesDeleted($product, $updatedValues): array
    {
        $is_deleted = false;
        $created_product_option_value_ids = $this->combinationRepositoryInterface->whereIn('sku_id', $product->skus()->pluck('id'))->pluck('product_option_value_id')->toArray();
        if (!$updatedValues) return [true, $created_product_option_value_ids];
        $filtered_updated_values = array_filter($updatedValues, function ($a) {
            return $a !== null;
        });
        if($created_product_option_value_ids != $filtered_updated_values)
            $is_deleted = true;

        return $is_deleted ? [true,array_diff($created_product_option_value_ids,$filtered_updated_values)] : [false, null];
    }

    /**
     * @param ProductUpdateDetailsObjects[] $skus
     * @return array
     */
    private function checkIsValuesAdded(array $skus): array
    {
        $product_option_value_ids = [];
        foreach ($skus as $sku) {
            $combination = $sku->getCombination();
            if (!$combination) return [false, null];
            foreach ($combination as $option_values) {
                array_push($product_option_value_ids, $option_values->getOptionValueId());
            }
        }
        return [in_array(null, $product_option_value_ids, true), $product_option_value_ids];
    }

    private function checkIsOptionChanged($first_options_values): bool
    {
        $updated_option_ids = [];
        foreach ($first_options_values as $option_value) {
            array_push($updated_option_ids, $option_value->getOptionId());
        }
        return $this->containsOnlyNull($updated_option_ids);
    }

    private function containsOnlyNull($input): bool
    {
        return empty(array_filter($input, function ($a) {
            return $a !== null;
        }));
    }

    public function getDeletedValues(): ?array
    {
        return $this->deletedValues;
    }
}
