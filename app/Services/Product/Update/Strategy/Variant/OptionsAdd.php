<?php namespace App\Services\Product\Update\Strategy\Variant;


use App\Services\Product\Update\Strategy\ProductUpdateStrategy;

class OptionsAdd extends OptionsUpdate
{

    public function update() : ProductUpdateStrategy
    {
        $this->deleteBatchStock();
        $this->deleteSkuAndCombination();
        $this->deleteProductChannels();
        $this->createNewProductVariantsData();
    }
}
