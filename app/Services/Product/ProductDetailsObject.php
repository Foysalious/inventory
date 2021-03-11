<?php namespace App\Services\Product;


class ProductDetailsObject
{
    private $productDetails;
    private $hasVariant = true;

    public function setProductDetails($productDetails)
    {
        $this->productDetails =  json_decode($productDetails);
        return $this;
    }

    public function getProductDetails()
    {
        return $this->productDetails;
    }

    public function hasVariants()
    {
        return $this->hasVariant;
    }

    public function build()
    {
        if(is_null($this->productDetails[0]->combination))
        {
            $this->hasVariant = false;
        }
    }

    public function validate()
    {

    }





}
