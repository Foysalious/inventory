<?php namespace App\Services\Product\Update\Strategy\Variant;


use App\Services\Product\Update\Strategy\ProductUpdateStrategy;

class ValuesAdd extends ValuesUpdate
{
    public function update(): ProductUpdateStrategy
    {
        $this->operationsForValueAdd();
        $this->deleteProductChannels();
        $this->createProductChannel($this->product->id, $this->channels);
    }
}
