<?php namespace App\Services\Product;


use App\Exceptions\ProductDetailsPropertyValidationError;

class ProductDetailsObject
{
    private $productDetail;
    private  $combination;
    private $stock;
    private $channelData;
    private $hasVariant;
    private $isPercentage;
    private $discount;
    private $details;

    /**
     * @param mixed $details
     * @return ProductDetailsObject
     */
    public function setDetails($details)
    {
        $this->details = $details;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * @param mixed $isPercentage
     * @return ProductDetailsObject
     */
    public function setIsPercentage($isPercentage)
    {
        $this->isPercentage = $isPercentage;
        return $this;
    }

    /**
     * @param mixed $discount
     * @return ProductDetailsObject
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsPercentage()
    {
        return $this->isPercentage;
    }

    /**
     * @return mixed
     */
    public function getDiscount()
    {
        return $this->discount;
    }


    public function setProductDetail($productDetail)
    {
        $this->productDetail =  $productDetail;
        return $this;
    }

    public function hasVariant($hasValiant)
    {
        $this->hasVariant = $hasValiant;
        return $this;
    }

    public function build()
    {
        if(!$this->validate())
            throw new ProductDetailsPropertyValidationError();
        if($this->hasVariant)
            $this->setCombination();
        $this->setStock();
        $this->setChannelData();
        return $this;
    }


    /**
     * @return bool|mixed
     * @throws ProductDetailsPropertyValidationError
     */
    public function validate()
    {
        return (property_exists($this->productDetail,'combination') && property_exists($this->productDetail,'stock')
            && property_exists($this->productDetail,'channel_data'));
    }


    public function setCombination()
    {

        $final = [];
        foreach ($this->productDetail->combination as $option_value) {
            array_push($final, app(CombinationDetailsObject::class)->setCombinationDetail($option_value)->build());
        }

        $this->combination = $final;
        return $this;
    }

    public function getCombination()
    {
        return $this->combination;
    }

    public function setStock()
    {
        $this->stock = $this->productDetail->stock;
        return $this;
    }

    public function getStock()
    {
        return $this->stock;
    }
    public function setChannelData()
    {
        $final = [];
        foreach ($this->productDetail->channel_data as $channel_data) {
            array_push($final, app(ChannelDetailsObject::class)->setChannelDetails($channel_data)->build());
        }
        $this->channelData = $final;
        return $this;

    }
    public function getChannelData()
    {
        return $this->channelData;
    }

}
