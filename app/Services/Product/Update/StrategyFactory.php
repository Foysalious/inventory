<?php namespace App\Services\Product\Update;


use App\Interfaces\CombinationRepositoryInterface;
use App\Interfaces\OptionRepositoryInterface;
use App\Interfaces\ProductChannelRepositoryInterface;
use App\Interfaces\ProductOptionRepositoryInterface;
use App\Interfaces\ProductOptionValueRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use App\Interfaces\SkuRepositoryInterface;
use App\Interfaces\ValueRepositoryInterface;
use App\Services\Product\Update\Strategy\NonVariant\NonVariant;
use App\Services\Product\Update\Strategy\Variant\OptionsAdd;
use App\Services\Product\Update\Strategy\Variant\OptionsDelete;
use App\Services\Product\Update\Strategy\Variant\OptionsUpdate;
use App\Services\Product\Update\Strategy\Variant\ValuesAdd;
use App\Services\Product\Update\Strategy\Variant\ValuesDelete;
use App\Services\Product\Update\Strategy\Variant\ValuesUpdate;

class StrategyFactory
{
    protected $optionRepositoryInterface;
    protected $valueRepositoryInterface;
    protected $productOptionRepositoryInterface;
    protected $productOptionValueRepositoryInterface;
    protected $combinationRepositoryInterface;
    protected $productChannelRepositoryInterface;
    protected $skuRepositoryInterface;
    protected ProductRepositoryInterface $productRepositoryInterface;
    private $deletedValues = null;

    public function __construct(ProductRepositoryInterface $productRepositoryInterface, OptionRepositoryInterface $optionRepositoryInterface, ValueRepositoryInterface  $valueRepositoryInterface, ProductOptionRepositoryInterface $productOptionRepositoryInterface,
                                ProductOptionValueRepositoryInterface $productOptionValueRepositoryInterface, CombinationRepositoryInterface  $combinationRepositoryInterface,
                                ProductChannelRepositoryInterface $productChannelRepositoryInterface, SkuRepositoryInterface $skuRepositoryInterface)
    {
        $this->productRepositoryInterface = $productRepositoryInterface;
        $this->optionRepositoryInterface = $optionRepositoryInterface;
        $this->valueRepositoryInterface = $valueRepositoryInterface;
        $this->combinationRepositoryInterface = $combinationRepositoryInterface;
        $this->productOptionRepositoryInterface = $productOptionRepositoryInterface;
        $this->productOptionValueRepositoryInterface = $productOptionValueRepositoryInterface;
        $this->productChannelRepositoryInterface =  $productChannelRepositoryInterface;
        $this->skuRepositoryInterface = $skuRepositoryInterface;

    }

    public function getStrategy($product, $skus, $has_variant)
    {
        list($is_new_values_added, $updatedValues) = $this->checkIsValuesAdded($skus);
        list($is_values_deleted, $this->deletedValues) = $this->checkIsValuesDeleted($product, $updatedValues);
        $created_with_variants = $this->productOptionRepositoryInterface->where('product_id',$product->id)->count() > 0;
        if(!$created_with_variants && !$has_variant)
            return app(NonVariant::class);
        elseif(!$created_with_variants && $has_variant)
            return app(OptionsAdd::class);
        elseif($created_with_variants && !$has_variant)
            return app(OptionsDelete::class);
        elseif($this->checkIsOptionChanged($skus[0]->getCombination()))
            return app(OptionsUpdate::class);
        elseif ($is_new_values_added && $is_values_deleted)
            return app(ValuesUpdate::class);
        elseif ($is_new_values_added && !$is_values_deleted)
            return app(ValuesAdd::class);
        else
            return app(ValuesDelete::class);

    }

    private function checkIsValuesDeleted($product, $updatedValues)
    {
        $is_deleted = false;
        $created_product_option_value_ids = $this->combinationRepositoryInterface->whereIn('sku_id', $product->skus()->pluck('id'))->pluck('product_option_value_id')->toArray();
        $filtered_updated_values = array_filter($updatedValues, function ($a) {
            return $a !== null;
        });
        if($created_product_option_value_ids != $filtered_updated_values)
            $is_deleted = true;

        return $is_deleted ? [true,array_diff($created_product_option_value_ids,$filtered_updated_values)] : [false,null];
    }

    private function checkIsValuesAdded($skus)
    {
        $product_option_value_ids = [];
        foreach ($skus as $sku) {
            $combination = $sku->getCombination();
            foreach ($combination as $option_values) {
                array_push($product_option_value_ids, $option_values->getOptionValueId());
            }
        }
        return [in_array(null, $product_option_value_ids, true), $product_option_value_ids];
    }

    private function checkIsOptionChanged($first_options_values)
    {
        $updated_option_ids = [];
        foreach ($first_options_values as $option_value) {
            array_push($updated_option_ids, $option_value->getOptionId());
        }
        return $this->containsOnlyNull($updated_option_ids);
    }

    private function containsOnlyNull($input)
    {
        return empty(array_filter($input, function ($a) {
            return $a !== null;
        }));
    }

    public function getDeletedValues()
    {
        return $this->deletedValues;
    }
}
