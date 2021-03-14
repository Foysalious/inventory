<?php namespace App\Services\Product;


class ProductCreateRequest
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

    public function get()
    {
        $final = [];
        foreach( $this->productDetails as $productDetail)
        {
            $productRequestObject = app(ProductDetailsObject::class)->setProductDetail($productDetail)->build();
            array_push($final,$productRequestObject);
        }
        return [$this->hasVariant(),$final];
    }

}
