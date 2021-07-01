<?php namespace App\Services\Product\Update\Operations;


class VariantsAdd extends OptionsUpdated
{
    public function apply()
    {
        $this->productStockBatchUpdater->deleteBatchStock($this->product);
        $this->deleteSkuAndCombination();
        $this->deleteProductChannels();
        $this->createNewProductVariantsData();
    }

}
