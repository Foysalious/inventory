<?php namespace App\Services\Product\Update\Strategy\NonVariant;

use App\Services\Product\ProductUpdateDetailsObjects;
use App\Services\Product\Update\Strategy\ProductUpdateStrategy;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class NonVariant extends NonVariantProductUpdate implements ProductUpdateStrategy
{
    /**
     * @throws UnknownProperties
     */
    public function update()
    {
        $sku = $this->skuRepository->where('product_id',$this->product->id)->first();
        /** @var ProductUpdateDetailsObjects $productUpdateObject */
        $productUpdateObject = $this->updateDataObjects[0];
        $sku_channels = $productUpdateObject->getChannelData();
        $this->updateSkuChannels($sku_channels, $sku->id);
        $this->deleteProductChannels();
        $this->createProductChannel($this->product->id, $sku_channels);
        $this->updateStock($sku, $this->updateDataObjects);
    }


}
