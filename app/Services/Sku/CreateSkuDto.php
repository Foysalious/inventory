<?php namespace App\Services\Sku;


use Spatie\DataTransferObject\DataTransferObject;

class CreateSkuDto extends DataTransferObject
{
    public string $name;
    public int $product_id;
    public float $stock;
    public ?float $weight;
    public ?string $weight_unit;
}
