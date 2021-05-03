<?php namespace App\Services\Product;


use App\Interfaces\SkuChannelRepositoryInterface;
use App\Models\Product;

class ProductCalculator
{
    private Product $product;
    private SkuChannelRepositoryInterface $skuChannelRepositoryInterface;
    private $channel;

    public function __construct(SkuChannelRepositoryInterface $skuChannelRepositoryInterface)
    {
        $this->skuChannelRepositoryInterface = $skuChannelRepositoryInterface;
    }

    public function setProduct(Product $product)
    {
        $this->product = $product;
        return $this;
    }

    public function setChannel($channel)
    {
        $this->channel = $channel;
        return $this;
    }

    public function getOriginalPrice()
    {
        $sku_ids = $this->product->skus->pluck('id')->toArray();
        return $this->skuChannelRepositoryInterface->whereIn('sku_id',$sku_ids)->where('channel_id',$this->channel)->min('price');
    }

}
