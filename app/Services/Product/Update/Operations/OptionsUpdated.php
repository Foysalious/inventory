<?php namespace App\Services\Product\Update\Operations;


use App\Interfaces\ProductRepositoryInterface;
use App\Models\Product;
use App\Services\Product\CombinationCreator;
use App\Services\Product\ProductChannelCreator;
use App\Services\Product\ProductOptionCreator;
use App\Services\Product\ProductOptionValueCreator;


class OptionsUpdated
{
    /**
     * @var ProductRepositoryInterface
     */

    private ProductRepositoryInterface $productRepositoryInterface;
    /**
     * @var ProductOptionCreator
     */
    private ProductOptionCreator $productOptionCreator;
    /**
     * @var ProductOptionValueCreator
     */
    private ProductOptionValueCreator $productOptionValueCreator;
    /**
     * @var CombinationCreator
     */
    private CombinationCreator $combinationCreator;
    /**
     * @var ProductChannelCreator
     */
    protected ProductChannelCreator $productChannelCreator;

    /**
     * @var Product $product
     */
    protected $product;

    protected $updateDataObejects;
    protected $hasVariants;

    public function __construct(ProductRepositoryInterface $productRepositoryInterface, ProductOptionCreator $productOptionCreator,
                                ProductOptionValueCreator $productOptionValueCreator, CombinationCreator $combinationCreator,
                                ProductChannelCreator $productChannelCreator)
    {
        $this->productRepositoryInterface = $productRepositoryInterface;
        $this->productOptionCreator = $productOptionCreator;
        $this->productOptionValueCreator = $productOptionValueCreator;
        $this->combinationCreator = $combinationCreator;
        $this->productChannelCreator = $productChannelCreator;
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param mixed $product
     */
    public function setProduct($product)
    {
        $this->product = $product;
        return $this;
    }

    public function setHasVariants($hasVariants)
    {
        $this->hasVariants = $hasVariants;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUpdateDataObejects()
    {
        return $this->updateDataObejects;
    }

    /**
     * @param mixed $updateDataObejects
     */
    public function setUpdatedDataObjects($updateDataObejects)
    {
        $this->updateDataObejects = $updateDataObejects;
        return $this;
    }


    public function apply()
    {
        $this->deleteProductOptions();
        $this->deleteSkuAndCombination();
        $this->deleteProductChannels();
        $this->createNewProductVariantsData();
    }

    protected function deleteProductOptions()
    {
        $this->product->productOptions()->get()->each(function($productOption){
            $productOption->productOptionValues()->delete();
        });
        return $this->product->productOptions()->delete();
    }

    protected function deleteProductChannels()
    {
        return $this->product->productChannels()->delete();
    }

    protected function deleteSkuAndCombination()
    {
        $this->product->skus()->get()->each(function($sku){
           if($this->hasVariants) $sku->combinations()->delete();
            $sku->skuChannels()->delete();
        });
        return $this->product->skus()->delete();
    }

    protected function createNewProductVariantsData()
    {

        $product = $this->product;
        $all_channels = [];

        foreach ($this->updateDataObejects as $productDetailObject) {
            $combinations = $productDetailObject->getCombination();
            $product_option_value_ids = [];
            $values = [];
            foreach ($combinations as $combination) {
                $option_name = $combination->getOptionName();
                $product_option = $this->createProductOptions($product->id, $option_name);
                $value_name = $combination->getOptionValueName();
                $product_option_value = $this->createProductOptionValues($product_option->id, $value_name);
                array_push($product_option_value_ids, $product_option_value->id);
                array_push($values, $value_name);
            }
            $sku = $this->createSku($product, $values, $product->id, $productDetailObject->getStock());
            $channels = $this->createSkuChannels($sku, $productDetailObject->getChannelData());
            array_push($all_channels,$channels);
            $this->createCombination($sku->id, $product_option_value_ids);
        }
        $all_channels = array_merge(... $all_channels);
        $this->createProductChannel($all_channels, $product->id);
    }

    /**
     * @param $product
     * @param $values
     * @param $product_id
     * @param $stock
     * @return mixed
     */
    private function createSku($product, $values, $product_id, $stock)
    {
        $sku_data = [
            'name' => implode("-", $values),
            'product_id' => $product_id,
            'stock' => $stock,
        ];
        return $product->skus()->create($sku_data);
    }

    /**
     * @param $channels
     * @param $product_id
     *
     * @return mixed
     */
    private function createProductChannel($channels, $product_id)
    {
        $product_channels = [];
        $channels = array_unique($channels);
        foreach ($channels as $channel) {
            array_push($product_channels, [
                'channel_id' => $channel,
                'product_id' => $product_id
            ]);
        }
        return $this->productChannelCreator->setData($product_channels)->store();
    }

    /**
     * @param $sku_id
     * @param $product_option_value_ids
     * @return mixed
     */
    private function createCombination($sku_id, $product_option_value_ids)
    {

        $combinations = collect($product_option_value_ids)->map(function ($product_option_value_id) use ($sku_id) {
            return [
                'product_option_value_id' => $product_option_value_id,
                'sku_id' => $sku_id
            ];
        });
        return $this->combinationCreator->setData($combinations->toArray())->store();
    }

    /**
     * @param $sku
     * @param $channel_data
     * @return mixed
     */
    private function createSkuChannels($sku, $channel_data)
    {
        $channels = [];
        $data = [];
        foreach ($channel_data as $channel) {
            array_push($data, [
                'sku_id' => $sku->id,
                'channel_id' => $channel->getChannelId(),
                'cost' => $channel->getCost() ?: 0,
                'price' => $channel->getPrice() ?: 0,
                'wholesale_price' => $channel->getWholeSalePrice() ?: null
            ]);
            array_push($channels,$channel->getChannelId());
        }
         $sku->skuChannels()->insert($data);
        return $channels;

    }
    /**
     * @param $product_id
     * @param $option_name
     * @return mixed
     */
    private function createProductOptions($product_id, $option_name)
    {
        return $this->productOptionCreator->setProductId($product_id)->setOptionName($option_name)->create();
    }
    /**
     * @param $product_option_id
     * @param $value_name
     * @return mixed
     */
    private function createProductOptionValues($product_option_id, $value_name)
    {
        return $this->productOptionValueCreator->setProductOptionId($product_option_id)->setValueName($value_name)->create();
    }

}
