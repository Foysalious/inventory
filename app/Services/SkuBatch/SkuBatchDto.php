<?php namespace App\Services\SkuBatch;

use Spatie\DataTransferObject\DataTransferObject;

class SkuBatchDto extends DataTransferObject
{
    protected int $sku_id;
    protected float $stock;
    protected float $cost;

}
