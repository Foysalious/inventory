<?php namespace App\Services\Product\Update\Strategy\Variant;



class ValuesDelete extends ValuesUpdate
{
    public function update()
    {
        $this->deleteDiscardedCombinations();
        $this->operationsForOldValues();
        $this->deleteProductChannels();
        $this->createProductChannel($this->product->id, $this->channels);
    }
}
