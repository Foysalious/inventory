<?php namespace App\Services\Product\Update\Strategy;


use App\Models\Product;

interface ProductUpdateStrategy
{
    public function setProduct(Product $product): ProductUpdateStrategy;

    public function setHasVariant(bool $hasVariant): ProductUpdateStrategy;

    public function setUpdatedDataObjects(array $updateDataObjects): ProductUpdateStrategy;

    public function setDeletedValues(?array $deletedValues): ProductUpdateStrategy;

    public function setAccountingInfo(?array $accountingInfo): ProductUpdateStrategy;

    public function update();
}
