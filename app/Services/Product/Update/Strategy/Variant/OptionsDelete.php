<?php namespace App\Services\Product\Update\Strategy\Variant;


use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class OptionsDelete extends OptionsUpdate
{

    public function update()
    {
        $this->deleteBatchStock();
        $this->deleteProductOptions();
        $this->deleteSkuAndCombination();
        $this->deleteProductChannels();
        $this->createSkuAndSkuChannels();
    }

    /**
     * @throws UnknownProperties
     */
    public function createSkuAndSkuChannels(): void
    {
        $sku = $this->createSku($this->product, $this->updateDataObjects[0]);
        $channels = $this->createSKUChannels($sku, $this->updateDataObjects[0]->getChannelData());
        $this->createProductChannel($this->product->id, $channels);
        $this->productStockBatchUpdater->createBatchStock($sku, $this->updateDataObjects[0]);
    }
}
