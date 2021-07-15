<?php namespace App\Services\Product\Update\Strategy\Variant;


use App\Services\Product\Update\Strategy\ProductUpdateStrategy;

class ValuesDelete extends ValuesUpdate
{
    public function update(): ProductUpdateStrategy
    {
        $this->deleteDiscardedCombinations();
        $this->operationsForOldValues();
        $this->deleteProductChannels();
        $this->createProductChannel($this->product->id, $this->channels);
    }
}
