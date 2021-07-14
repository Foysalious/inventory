<?php namespace App\Services\Product\Update\Strategy\Variant;


class ValuesAdd extends ValuesUpdate
{
    public function update()
    {
        $this->operationsForValueAdd();
        $this->deleteProductChannels();
        $this->createProductChannel($this->product->id, $this->channels);
    }
}
