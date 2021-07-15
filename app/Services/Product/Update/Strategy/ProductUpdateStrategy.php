<?php namespace App\Services\Product\Update\Strategy;


use App\Models\Product;

interface ProductUpdateStrategy
{
    public function setProduct(Product $product);
    public function setUpdatedDataObjects(array $updateDataObjects);
    public function setDeletedValues(?array $deletedValues);
    public function update();
}
