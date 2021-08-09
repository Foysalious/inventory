<?php namespace App\Services\Product;


use App\Interfaces\ProductOptionRepositoryInterface;
use App\Interfaces\SkuRepositoryInterface;
use App\Models\Product;
use App\Models\Sku;

class ProductCombinationService
{
    private SkuRepositoryInterface $skuRepositoryInterface;
    private Product $product;

    /**
     * ProductCombinationService constructor.
     * @param SkuRepositoryInterface $skuRepositoryInterface
     */
    public function __construct(SkuRepositoryInterface $skuRepositoryInterface, ProductOptionRepositoryInterface $productOptionRepositoryInterface)
    {
        $this->skuRepositoryInterface = $skuRepositoryInterface;
        $this->productOptionRepositoryInterface= $productOptionRepositoryInterface;
    }

    /**
     * @param Product $product
     * @return ProductCombinationService
     */
    public function setProduct(Product $product): ProductCombinationService
    {
        $this->product = $product;
        return $this;
    }

    public function getCombinationDataForWebstore()
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
                        'option_value_name' => $product_option_value->name,
                        'option_value_details' => json_decode($product_option_value->details)
                    ]);
                });
            }
            if (!isset($sku_data['combination'])) $sku_data['combination'] = [];
            $sku_data['combination'] = !empty($temp)? $temp :null;
            if (!isset($sku_data['stock'])) $sku_data['stock'] = [];
            $sku_data['stock'] = $sku->stock();
            $sku_data['purchase_price'] = $sku->getPurchaseUnitPrice();
            $temp = [];
            if($sku->skuChannels)
            {
                $sku->skuChannels->each(function ($sku_channel) use (&$temp, $sku) {
                    /** @var  $priceCalculation PriceCalculation */
                    $priceCalculation = app(PriceCalculation::class);
                    $priceCalculation->setSkuChannel($sku_channel);
                    if($sku_channel->channel_id == 2)
                        $temp =  [
                            "sku_channel_id" => $sku_channel->id,
                            "channel_id" => $sku_channel->channel_id,
                            "purchase_price" => $sku->getPurchaseUnitPrice(),
                            "original_price" => $priceCalculation->getOriginalUnitPriceWithVat(),
                            "discounted_price" => $priceCalculation->getDiscountedUnitPrice(),
                            "discount" => $priceCalculation->getDiscountAmount(),
                            "is_discount_percentage" => $priceCalculation->isDiscountPercentage(),
                            "wholesale_price" => $priceCalculation->getWholesalePriceWithVat()
                        ];
                });
            }
            if (!isset($sku_data['channel_data'])) $sku_data['channel_data'] = [];
            $sku_data['channel_data'] = !empty($temp)? $temp :null;
            array_push($data, $sku_data);
        }
        return [$options,$data];

    }

    public function getCombinationData(): array
    {
        $data = [];
        $skus = $this->product->skus;
        /** @var Sku $sku */
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
            $sku_data['sku_id'] = $sku->id;
            if (!isset($sku_data['combination'])) $sku_data['combination'] = [];
            $sku_data['combination'] = !empty($temp) ? $temp : null;
            if (!isset($sku_data['stock'])) $sku_data['stock'] = [];
            $sku_data['stock'] = $sku->stock();
            $sku_data['purchase_price'] = $sku->getPurchaseUnitPrice();
            $sku_data['last_batch_stock'] = $sku->getLastBatchStock();
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
                        "purchase_price" => $priceCalculation->getPurchaseUnitPrice(),
                        "original_price" => $priceCalculation->getOriginalUnitPrice(),
                        "discounted_price" => $priceCalculation->getDiscountedUnitPrice(),
                        "discount" => $priceCalculation->getDiscountAmount(),
                        "is_discount_percentage" => $priceCalculation->isDiscountPercentage(),
                        "wholesale_price" => $priceCalculation->getWholesalePriceWithVat()
                    ]);
                });
            }
            if (!isset($sku_data['channel_data'])) $sku_data['channel_data'] = [];
            $sku_data['channel_data'] = !empty($temp) ? $temp : null;
            array_push($data, $sku_data);
        }
        return $data;
    }
}
