<?php namespace App\Services\Product;

use App\Exceptions\ProductDetailsPropertyValidationError;

class ChannelUpdateDetailsObjects
{

    private $channelDetails;
    private $price;
    private $cost;
    private $wholeSalePrice;
    private $channelId;
    private $skuChannelId;

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

    public function getChannelId()
    {
        return $this->channelId;
    }

    public function getSkuChannelId()
    {
        return $this->skuChannelId;
    }

    /**
     * @return $this
     * @throws ProductDetailsPropertyValidationError
     */
    public function build()
    {
        if(!$this->validate())
            throw new ProductDetailsPropertyValidationError();
        $this->cost = $this->channelDetails->cost;
        $this->price = $this->channelDetails->price;
        $this->wholeSalePrice = $this->channelDetails->wholesale_price;
        $this->channelId = $this->channelDetails->channel_id;
        $this->skuChannelId = $this->channelDetails->sku_channel_id;
        return $this;
    }


    public function validate()
    {
        return (property_exists( $this->channelDetails,'channel_id')) && (property_exists( $this->channelDetails,'cost'))
            && (property_exists( $this->channelDetails,'price')) && (property_exists( $this->channelDetails,'wholesale_price'))
            && (property_exists( $this->channelDetails,'sku_channel_id'));
    }


}
