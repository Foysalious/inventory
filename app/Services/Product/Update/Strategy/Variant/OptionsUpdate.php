<?php namespace App\Services\Product\Update\Strategy\Variant;


use App\Models\ProductOption;
use App\Models\Sku;
use App\Models\SkuChannel;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class OptionsUpdate extends VariantProductUpdate
{
    /**
     * @throws UnknownProperties
     */
    public function update()
    {
        $this->deleteProductOptions();
        $this->deleteSkuAndCombination();
        $this->deleteProductChannels();
        $this->deleteBatchStock();
        $this->createNewProductVariantsData();
    }

    protected function deleteProductOptions()
    {
        $this->product->productOptions()->get()->each(function (ProductOption $productOption) {
            $productOption->productOptionValues()->delete();
        });
        return $this->product->productOptions()->delete();
    }

    protected function deleteSkuAndCombination()
    {
        $this->product->skus()->get()->each(function (Sku $sku) {
            if ($this->hasVariants) $sku->combinations()->delete();
            $sku->skuChannels()->get()->each(function (SkuChannel $skuChannel) {
                $skuChannel->discounts()->delete();
            });
            $sku->skuChannels()->delete();
        });
        return $this->product->skus()->delete();
    }

    /**
     * @throws UnknownProperties
     */
    protected function createNewProductVariantsData()
    {
        $product = $this->product;
        $all_channels = [];

        foreach ($this->updateDataObjects as $productDetailObject) {
            $combinations = $productDetailObject->getCombination();
            $product_option_value_ids = [];
            foreach ($combinations as $combination) {
                $option_name = $combination->getOptionName();
                $product_option = $this->createProductOptions($product->id, $option_name);
                $value_name = $combination->getOptionValueName();
                $product_option_value = $this->createProductOptionValues($product_option->id, $value_name);
                array_push($product_option_value_ids, $product_option_value->id);
            }
            $sku = $this->createSku($product, $productDetailObject);
            $channels = $this->createSkuChannels($sku, $productDetailObject->getChannelData());
            array_push($all_channels, $channels);
            $this->createCombination($sku->id, $product_option_value_ids);
            $this->productStockBatchUpdater->createBatchStock($sku, $productDetailObject);
        }
        $all_channels = array_merge(... $all_channels);
        $this->createProductChannel($product->id, $all_channels);
    }
}
