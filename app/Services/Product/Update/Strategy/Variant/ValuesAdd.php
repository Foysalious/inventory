<?php namespace App\Services\Product\Update\Strategy\Variant;


use App\Services\Product\Update\Strategy\ProductUpdateStrategy;

class ValuesAdd extends ValuesUpdate implements ProductUpdateStrategy
{
    public function update()
    {
        $this->operationsForValueAdd();
        $this->deleteProductChannels();
        $this->createProductChannel($this->product->id, $this->channels);
    }
}
