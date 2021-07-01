<?php namespace App\Services\Product\Update\Operations;


class NonVariant extends ValuesUpdated
{
    public function apply()
    {
        $sku = $this->skuRepository->where('product_id',$this->product->id)->first();
        $sku_channels = $this->updateDataObejects[0]->getChannelData();
        $this->updateSkuChannels($sku_channels,$sku->id);
        $this->resolveProductChannel();
        $this->updateStock($sku, $this->updateDataObejects);
    }
}
