<?php namespace App\Services\Product\Update\Strategy\NonVariant;


use App\Services\Product\Update\Strategy\ProductUpdate;
use App\Services\Product\Update\Strategy\ProductUpdateStrategy;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class NonVariantProductUpdate extends ProductUpdate
{
    /**
     * @throws UnknownProperties
     */
    public function update() : ProductUpdateStrategy
    {
        $sku = $this->skuRepository->where('product_id',$this->product->id)->first();
        $productUpdateObject = $this->updateDataObjects[0];
        $sku_channels = $productUpdateObject->getChannelData();
        $this->updateSkuChannels($sku_channels, $sku->id);
        $this->deleteProductChannels();
        $this->createProductChannel($this->product->id, $this->channels);
        $this->updateStock($sku, $this->updateDataObjects);
    }
}
