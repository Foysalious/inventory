<?php namespace App\Services\Product;


class ProductUpdateRequest
{
    private $productDetails;

    public function setProductDetails($productDetails)
    {
        $this->productDetails = json_decode($productDetails);
        return $this;
    }

    /**
     * @return bool
     */
    public function hasVariant()
    {
        return !is_null($this->productDetails[0]->combination);
    }




}
