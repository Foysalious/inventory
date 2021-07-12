<?php namespace App\Services\Product\Update\Strategy\Variant;


use App\Services\Product\Update\Strategy\ProductUpdateStrategy;

class OptionsAdd extends OptionsUpdate implements ProductUpdateStrategy
{

    public function update()
    {
        $this->deleteBatchStock();
        $this->deleteSkuAndCombination();
        $this->deleteProductChannels();
        $this->createNewProductVariantsData();
    }
}
