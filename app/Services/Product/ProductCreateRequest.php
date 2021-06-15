<?php namespace App\Services\Product;


use App\Exceptions\ProductDetailsPropertyValidationError;

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

    /**
     * @return array
     * @throws ProductDetailsPropertyValidationError
     */
    public function get()
    {
        $has_variant  = $this->hasVariant();
        $final = [];
        foreach( $this->productDetails as $productDetail)
        {
            /** @var ProductDetailsObject $productRequestObject */
            $productRequestObject = app(ProductDetailsObject::class);
            $productRequestObject = $productRequestObject->hasVariant($has_variant)->setProductDetail($productDetail)->build();
            array_push($final, $productRequestObject);
        }
        return [$has_variant,$final];
    }

}
