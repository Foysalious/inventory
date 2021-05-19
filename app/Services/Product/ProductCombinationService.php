<?php namespace App\Services\Product;


use App\Interfaces\ProductOptionRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use App\Interfaces\SkuRepositoryInterface;

class ProductCombinationService
{
    private $productOptionRepositoryInterface;
    private $skuRepositoryInterface;
    private $product;

    /**
     * ProductCombinationService constructor.
     * @param $productOptionRepositoryInterface
     * @param $skuRepositoryInterface
     */
    public function __construct(ProductOptionRepositoryInterface $productOptionRepositoryInterface, SkuRepositoryInterface $skuRepositoryInterface)
    {
        $this->productOptionRepositoryInterface = $productOptionRepositoryInterface;
        $this->skuRepositoryInterface = $skuRepositoryInterface;
    }


    public function setProduct($product)
    {
        $this->product = $product;
        return $this;
    }

    public function getCombinationData()
    {
        $data = [];
        $options = $this->productOptionRepositoryInterface->where('product_id',$this->product->id)->pluck('name');
        $skus = $this->skuRepositoryInterface->where('product_id', $this->product->id)->with('combinations')->get();


        foreach ($skus as $sku) {
            $sku_data = [];
            $temp = [];
            if($sku->combinations)
            {
                $sku->combinations->each(function ($combination) use (&$sku_data, &$temp, &$data) {
                    $product_option_value = $combination->productOptionValue;
                    $product_option=$product_option_value->productOption;

                    array_push($temp, [
                        'option_id' => $product_option_value->productOption->id,
                        'option_name' => $product_option_value->productOption->name,
                        'option_value_id' => $product_option_value->id,
                        'option_value_name' => $product_option_value->name
                    ]);
                });
            }

            if (!isset($sku_data['combination'])) $sku_data['combination'] = [];
            $sku_data['combination'] = !empty($temp)? $temp :null;
            if (!isset($sku_data['stock'])) $sku_data['stock'] = [];
            $sku_data['stock'] = $sku->stock;
            $temp = [];
            if($sku->skuChannels)
            {
                $sku->skuChannels->each(function ($sku_channel) use (&$temp) {
                    /** @var  $priceCalculation PriceCalculation */
                    $priceCalculation = app(PriceCalculation::class);
                    $priceCalculation->setSkuChannel($sku_channel);
                    array_push($temp, [
                        "sku_channel_id" => $sku_channel->id,
                        "channel_id" => $sku_channel->channel_id,
                        "purchase_price" => $sku_channel->cost,
                        "original_price" => $sku_channel->price,
                        "discounted_price" => $sku_channel->price - 5,
                        "discount" => 5,
                        "is_discount_percentage" => 0,
                        "wholesale_price" => $sku_channel->wholesale_price
                    ]);
                });
            }

            if (!isset($sku_data['channel_data'])) $sku_data['channel_data'] = [];
            $sku_data['channel_data'] = !empty($temp)? $temp :null;
            array_push($data, $sku_data);
        }
        return [$options,$data];
    }
}
