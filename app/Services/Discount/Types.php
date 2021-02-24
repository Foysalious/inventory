<?php namespace App\Services\Discount;


use App\Helper\ConstGetter;

class Types
{
    use ConstGetter;

    const PRODUCT = 'product';
    const PRODUCT_CHANNEL = 'product_channel';
    const SKU_CHANNEL = 'sku_channel';
    const SKU = 'sku';
    const COLLECTION = 'collection';
    const CATEGORY = 'category';


}
