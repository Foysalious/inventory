<?php namespace App\Services\Product\Update\Strategy\NonVariant;


use App\Services\Product\Update\Strategy\ProductUpdate;

abstract class NonVariantProductUpdate extends ProductUpdate
{
    public abstract function update();
}
