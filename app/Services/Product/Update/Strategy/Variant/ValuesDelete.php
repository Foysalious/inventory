<?php namespace App\Services\Product\Update\Strategy\Variant;


use App\Services\Product\Update\Strategy\ProductUpdateStrategy;

class ValuesDelete extends ValuesUpdate implements ProductUpdateStrategy
{
    public function update()
    {
        $this->deleteDiscardedCombinations();
        $this->operationsForOldValues();
        $this->deleteProductChannels();
        $this->createProductChannel($this->product->id, $this->channels);
    }
}
