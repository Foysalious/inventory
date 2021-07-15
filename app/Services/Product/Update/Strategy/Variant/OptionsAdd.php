<?php namespace App\Services\Product\Update\Strategy\Variant;


class OptionsAdd extends OptionsUpdate
{

    public function update()
    {
        $this->deleteBatchStock();
        $this->deleteSkuAndCombination();
        $this->deleteProductChannels();
        $this->createNewProductVariantsData();
    }
}
