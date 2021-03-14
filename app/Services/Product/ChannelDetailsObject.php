<?php


namespace App\Services\Product;


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
     * @param mixed $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * @param mixed $cost
     */
    public function setCost($cost)
    {
        $this->cost = $cost;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getWholeSalePrice()
    {
        return $this->wholeSalePrice;
    }

    /**
     * @param mixed $wholeSalePrice
     */
    public function setWholeSalePrice($wholeSalePrice)
    {
        $this->wholeSalePrice = $wholeSalePrice;
        return $this;
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
        return (property_exists( $this->channelDetails,['channel_id','cost','price','wholsale_price']))?: throw new CombinationDetailsPropertyValidationError();
    }

}
