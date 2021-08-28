<?php namespace App\Services\SkuBatch;

use Spatie\DataTransferObject\DataTransferObject;

class SkuBatchDto extends DataTransferObject
{
    public int $sku_id;
    public ?float $stock;
    public float $cost;
    public ?int $supplier_id;
    public ?string $from_account;

}
