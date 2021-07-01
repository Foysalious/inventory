<?php namespace App\Services\Product\Update\Operations;


class VariantsAdd extends OptionsUpdated
{
    public function apply()
    {
        $this->deleteBatchStock();
        $this->deleteSkuAndCombination();
        $this->deleteProductChannels();
        $this->createNewProductVariantsData();
    }

}
