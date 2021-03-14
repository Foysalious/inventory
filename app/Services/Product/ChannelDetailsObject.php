<?php namespace App\Services\Product;


use App\Exceptions\ProductDetailsPropertyValidationError;

class ChannelDetailsObject
{

    private $channelDetails;
    private $price;
    private $cost;
    private $wholeSalePrice;

    /**
     * @param mixed $channelDetails
     */
    public function setChannelDetails($channelDetails)
    {
        $this->channelDetails = $channelDetails;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return mixed
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * @return mixed
     */
    public function getWholeSalePrice()
    {
        return $this->wholeSalePrice;
    }

    public function build()
    {
        $this->validate();
        $this->cost = $this->channelDetails->cost;
        $this->price = $this->channelDetails->price;
        $this->wholeSalePrice = $this->channelDetails->wholesale_price;
        return $this;

    }

    public function validate()
    {
        return (property_exists( $this->channelDetails,'channel_id')) && (property_exists( $this->channelDetails,'cost'))
        && (property_exists( $this->channelDetails,'price')) && (property_exists( $this->channelDetails,'wholsale_price'))
            ?: throw new ProductDetailsPropertyValidationError();
    }

}
