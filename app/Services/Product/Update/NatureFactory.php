<?php namespace App\Services\Product\Update;


use App\Interfaces\CombinationRepositoryInterface;
use App\Interfaces\OptionRepositoryInterface;
use App\Interfaces\ProductChannelRepositoryInterface;
use App\Interfaces\ProductOptionRepositoryInterface;
use App\Interfaces\ProductOptionValueRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use App\Interfaces\SkuRepositoryInterface;
use App\Interfaces\ValueRepositoryInterface;
use App\Services\Product\UpdateNature;

class NatureFactory
{
    protected $optionRepositoryInterface;
    protected $valueRepositoryInterface;
    protected $productOptionRepositoryInterface;
    protected $productOptionValueRepositoryInterface;
    protected $combinationRepositoryInterface;
    protected $productChannelRepositoryInterface;
    protected $skuRepositoryInterface;
    protected ProductRepositoryInterface $productRepositoryInterface;
    private $deletedValues;

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

    public function getNature($product, $skus)
    {
        if ($this->checkIsOptionChanged($skus[0]->getCombination()))
            return UpdateNature::OPTIONS_CHANGED;
        list($is_new_values_added, $updatedValues) = $this->checkIsValuesAdded($skus);
        list($is_values_deleted,$deleted_values) = $this->checkIsValesDeleted($product, $updatedValues);
        $this->deletedValues = $deleted_values;

        if ($is_new_values_added && $is_values_deleted)
            return UpdateNature::VALUE_ADD_DELETE;
        elseif ($is_new_values_added && !$is_values_deleted)
            return UpdateNature::VALUE_ADD;
        else
            return UpdateNature::VALUE_DELETE;

    }

    private function checkIsValesDeleted($product, $updatedValues)
    {
        $is_deleted = false;
        $created_product_option_value_ids = $this->combinationRepositoryInterface->whereIn('sku_id', $product->skus()->pluck('id'));

        $filtered_updated_values = array_filter($updatedValues, function ($a) {
            return $a !== null;
        });
        if($created_product_option_value_ids != $filtered_updated_values)
            $is_deleted = true;

        return $is_deleted ? [$is_deleted,array_diff($created_product_option_value_ids,$filtered_updated_values)] : [false,null];


    }

    private function checkIsValuesAdded($skus)
    {
        $product_option_value_ids = [];
        foreach ($skus as $sku) {
            $combination = $sku->getCombination();
            foreach ($combination as $options_values) {
                foreach ($options_values as $option_value)
                    array_push($product_option_value_ids, $option_value->getProductOptionValueId());
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


}