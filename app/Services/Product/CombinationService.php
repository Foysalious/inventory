<?php namespace App\Services\Product;


use App\Interfaces\ProductOptionRepositoryInterface;
use App\Interfaces\SkuRepositoryInterface;
use App\Models\Product;

class CombinationService
{
    /** @var Product */
    private $product;
    /** @var ProductOptionRepositoryInterface */
    private $productOptionRepositoryInterface;
    /** @var SkuRepositoryInterface */
    private $skuRepositoryInterface;

    /**
     * CombinationService constructor.
     * @param $productOptionRepositoryInterface
     * @param $skuRepositoryInterface
     */
    public function __construct(ProductOptionRepositoryInterface $productOptionRepositoryInterface, SkuRepositoryInterface $skuRepositoryInterface)
    {
        $this->productOptionRepositoryInterface = $productOptionRepositoryInterface;
        $this->skuRepositoryInterface = $skuRepositoryInterface;
    }

    /**
     * @param mixed $product
     * @return CombinationService
     */
    public function setProduct($product)
    {
        $this->product = $product;
        return $this;
    }

    /**
     * @param $product
     * @return array
     */
    public function getCombinationData($product)
    {
        $data = [];
        $options = $this->productOptionRepositoryInterface->where('product_id',$product->id)->pluck('name');
        $skus = $this->skuRepositoryInterface->where('product_id', $product->id)->with('combinations')->get();

        foreach ($skus as $sku) {
            $sku_data = [];
            $temp = [];
            if($sku->combinations)
            {
                $sku->combinations->each(function ($combination) use (&$sku_data, &$temp, &$data) {
                    $product_option_value = $combination->productOptionValue;
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
                    array_push($temp, [
                        "sku_channel_id" => $sku_channel->id,
                        "channel_id" => $sku_channel->channel_id,
                        "cost" => $sku_channel->cost,
                        "price" => $sku_channel->price,
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
