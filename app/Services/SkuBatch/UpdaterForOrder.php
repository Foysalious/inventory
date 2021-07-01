<?php


namespace App\Services\SkuBatch;


class UpdaterForOrder
{
    protected $sku;
    protected $quantity;

    /**
     * @param mixed $sku
     */
    public function setSku($sku)
    {
        $this->sku = $sku;
        return $this;
    }

    /**
     * @param mixed $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function stockIncrease()
    {
        $last_batch = $this->sku->batch()->orderByDesc('created_at')->first();
        $last_batch->stock = $last_batch->stock + $this->quantity;
        $last_batch->save();
    }

    public function stockDecrease()
    {
        $batches = $this->sku->batch()->orderBy('created_at')->get();
        foreach ($batches as $key=>$batch) {
            $is_last_batch = ($key+1) == count($batches);
            if($batch->stock >= $this->quantity) { //quantity less than batch size then substitute and break the loop
                $batch->stock = $batch->stock - $this->quantity;
                break;
            }
            if ($this->quantity > $batch->stock  && !$is_last_batch ) { //quantity greater than batch size and not last batch then batch zero, quantity decrease from batch size
                $this->quantity = $this->quantity - $batch->stock;
                $batch->stock = 0;
                continue;
            }
            if ($is_last_batch) { // last batch then stock go negative
                $batch->stock = $batch->stock - $this->quantity;
            }
        }
        $batches->each(function ($batch) {
            $batch->save();
        });
    }
}
